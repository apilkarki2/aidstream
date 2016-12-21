<?php namespace App\Services\PerfectViewer;

use App\Core\V202\Repositories\PerfectViewer\PerfectViewerRepository;
use App\Models\Activity\Activity;
use App\Models\HistoricalExchangeRate;
use Exception;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\DatabaseManager;
use Illuminate\Contracts\Logging\Log as Logger;

/**
 * Class PerfectActivityViewerManager
 * @package App\Services\PerfectViewer
 */
class PerfectViewerManager
{

    /**
     * @var PerfectViewerRepository
     */
    protected $perfectViewerRepo;

    /**
     * @var DatabaseManager
     */
    protected $database;

    /**
     * @var Guard
     */
    protected $auth;

    /**
     * @var
     */
    protected $published;

    /**
     * @var
     */
    protected $defaultFieldValues;

    /**
     * @var
     */
    protected $exchangeRatesBuilder;

    /**
     * @var HistoricalExchangeRate
     */
    protected $historicalExchangeRate;

    /**
     * PerfectActivityViewerManager constructor.
     *
     * @param PerfectViewerRepository $perfectViewerRepository
     * @param DatabaseManager         $databaseManager
     * @param Logger                  $logger
     * @param HistoricalExchangeRate  $historicalExchangeRate
     */
    public function __construct(
        PerfectViewerRepository $perfectViewerRepository,
        DatabaseManager $databaseManager,
        Logger $logger,
        HistoricalExchangeRate $historicalExchangeRate
    ) {
        $this->perfectViewerRepo      = $perfectViewerRepository;
        $this->database               = $databaseManager;
        $this->logger                 = $logger;
        $this->historicalExchangeRate = $historicalExchangeRate;
    }

    /**
     * Creates and stores snapshots, retrieves and store exchange rates with API, calculates total transaction and budgets
     *
     * @param $activity
     * @return PerfectViewerManager|bool
     */
    public function createSnapshot(Activity $activity)
    {
        try {

            $this->exchangeRatesBuilder = $this->perfectViewerRepo->getExchangeRatesBuilder();

            //activity data
            $this->defaultFieldValues = $activity->default_field_values;
            $orgId                    = $activity->organization_id;
            $activityId               = $activity->id;
            $published_to_registry    = $activity->published_to_registry;

            //transaction and budget
            $transactions        = $this->perfectViewerRepo->getTransactions($orgId);
            $totalTransaction    = $this->calculateTransaction($transactions);
            $totalBudget         = $this->calculateBudget(getVal($activity->toArray(), ['budget'], []));
            $activityTransaction = $this->perfectViewerRepo->getActivityTransactions($activityId);

            //organization data
            $organization  = $this->getOrg($orgId);
            $reporting_org = getVal($organization, [0, 'reporting_org'], []);
            $filename      = $this->perfectViewerRepo->getPublishedFileName(getVal((array) $organization[0], ['id'], []))->filename;
            $perfectOrg    = $this->makePerfectOrg($organization, $totalTransaction);

            $perfectData = $this->makeArray($activity, $reporting_org, $activityTransaction, $totalBudget);

            $this->database->beginTransaction();
            $this->perfectViewerRepo->storeActivity($this->transformToSchema($perfectData, $orgId, $activityId, $published_to_registry, $filename));
            $this->perfectViewerRepo->storeOrganization($perfectOrg);
            $this->database->commit();

            $this->logger->info(
                'Activity snapshot has been added',
                [
                    ' of activity '      => $activityId,
                    ' and organization ' => $orgId
                ]
            );

            return $this;

        } catch (Exception $exception) {
            $this->database->rollback();
            $this->logger->error(
                sprintf('Error creating snapshot due to %s', $exception->getMessage()),
                [
                    'Activity_identifier' => $activity->id,
                    'trace'               => $exception->getTraceAsString()
                ]
            );
        }

        return false;
    }

    /**
     * Transforms to ActivitySnapshot Schema for storing
     *
     * @param $perfectData
     * @param $orgId
     * @param $activityId
     * @param $published_to_registry
     * @param $filename
     * @return array
     */
    public function transformToSchema($perfectData, $orgId, $activityId, $published_to_registry, $filename)
    {
        return [
            'published_data'       => $perfectData,
            'org_id'               => $orgId,
            'activity_id'          => $activityId,
            'activity_in_registry' => $published_to_registry,
            'filename'             => $filename
        ];
    }

    /**
     * Makes Array for published_data of Activity Snapshot Schema
     *
     * @param $activity
     * @param $reporting_org
     * @param $transactions
     * @param $totalBudget
     * @return array
     */
    public function makeArray($activity, $reporting_org, $transactions, $totalBudget)
    {
        return [
            'title'                      => $activity->title,
            'description'                => $activity->description,
            'identifier'                 => $activity->identifier,
            'other_identifier'           => $activity->other_identifier,
            'activity_date'              => $activity->activity_date,
            'activity_status'            => $activity->activity_status,
            'budget'                     => $activity->budget,
            'contact_info'               => $activity->contact_info,
            'updated_at'                 => $activity->updated_at,
            'recipient_country'          => $activity->recipient_country,
            'recipient_region'           => $activity->recipient_region,
            'sector'                     => $activity->sector,
            'participating_organization' => $activity->participating_organization,
            'document_link'              => $activity->document_link,
            'reporting_org'              => $reporting_org,
            'transactions'               => $transactions,
            'totalBudget'                => $totalBudget,
        ];
    }

    /**
     * Provides reporting organization
     *
     * @param $orgId
     * @return
     */
    protected function getOrg($orgId)
    {
        return $this->perfectViewerRepo->getOrganization($orgId)->toArray();
    }

    /**
     * Calculates total budget of an activity
     *
     * @param $budget
     * @return int|string
     */
    protected function calculateBudget($budget)
    {
        $totalBudget['value']    = 0;
        $totalBudget['currency'] = '';

        foreach ($budget as $index => $value) {
            $totalBudget['value'] += $this->giveCorrectValue($value);
        }

        return $totalBudget;
    }

    /**
     * Calculates total transaction amount for each kind
     *
     * @param $transactions
     * @return mixed
     */
    protected function calculateTransaction($transactions)
    {
        $totalTransaction['total_incoming_funds'] = 0;
        $totalTransaction['total_commitments']    = 0;
        $totalTransaction['total_disbursements']  = 0;
        $totalTransaction['total_expenditures']   = 0;

        foreach ($transactions as $index => $transactionChild) {

            foreach ($transactionChild as $tranIndex => $transaction) {

                $value = $this->giveCorrectValue(getVal($transaction, ['transaction'], []));

                switch (getVal($transaction, ['transaction', 'transaction_type', 0, 'transaction_type_code'], '')) {

                    case 1:
                        $totalTransaction['total_incoming_funds'] += (float) $value;
                        break;

                    case 2:
                        $totalTransaction['total_commitments'] += (float) $value;
                        break;

                    case 3:
                        $totalTransaction['total_disbursements'] += (float) $value;
                        break;

                    case 4:
                        $totalTransaction['total_expenditures'] += (float) $value;
                        break;

                    default:
                        break;
                }
            }
        }

        return $totalTransaction;
    }


    /**
     * Provides data for given date
     *
     * @param $date
     * @return array
     */
    protected function getDate($date)
    {
        return json_decode($this->exchangeRatesBuilder->where('date', $date)->first(), true) ?: [];
    }

    /**
     * Provides correct converted rates for each exchange rates.
     *
     * @param $data
     * @return float
     */
    protected function giveCorrectValue($data)
    {
        $defaultCurrency = getVal($this->defaultFieldValues, ['0', 'default_currency']);
        $currency        = getVal($data, ['value', 0, 'currency'], '');
        if (array_key_exists('value_date', $data['value'][0])) {
            $date = getVal($data, ['value', 0, 'value_date'], '');
        } else {
            $date = getVal($data, ['value', 0, 'date'], '');
        }
        $amount = (float) getVal($data, ['value', 0, 'amount'], 0);

        $dbDate = $this->getDate($date);

        if (empty($dbDate)) {
            $dbDate = json_decode($this->exchangeRatesBuilder->where('date', '<', $date)->orderBy('date', 'desc')->first(), true) ?: [];
        }

        if ($currency != 'USD') {
            if ($currency == '') {
                if ($defaultCurrency != 'USD') {
                    //Return exchange rate for default currency not USD
                    $eRate = getVal($dbDate, ['exchange_rates', sprintf('%s', $defaultCurrency)], 1);

                    return $amount / $eRate;
                }

                //Return value for default currency USD
                return $amount;
            } else {
                //Return exchange rate for given currency based on USD
                $eRate = getVal($dbDate, ['exchange_rates', sprintf('%s', $currency)], 1);

                return $amount / $eRate;
            }
        }

        //Return USD amount
        return $amount;
    }

    /**
     * Provides Organization and its snapshot's Query Builder
     *
     * @return \App\Models\PerfectViewer\OrganizationSnapshot
     */
    public function organizationQueryBuilder()
    {
        return $this->perfectViewerRepo->organizationQueryBuilder();
    }

    /**
     * Provides Activity Snapshot Query Builder
     *
     * @return \App\Models\PerfectViewer\ActivitySnapshot
     */
    public function activityQueryBuilder()
    {
        return $this->perfectViewerRepo->activityQueryBuilder();

    }

    /**
     * Provides Activity Snapshot form Organization Id
     *
     * @param $orgId
     * @return mixed
     */
    public function getSnapshotWithOrgId($orgId)
    {
        return $this->perfectViewerRepo->getSnapshot($orgId);
    }

    /**
     * Provides Organization from Organization Id
     *
     * @param $orgId
     * @return mixed
     */
    public function getOrgWithOrgId($orgId)
    {
        return $this->perfectViewerRepo->getOrgWithId($orgId);
    }

    /**
     * Provides Array for Activity Snapshot Schema
     *
     * @param $organization
     * @param $totalTransaction
     * @return array
     */
    public function makePerfectOrg($organization, $totalTransaction)
    {
        return [
            'org_id'                => getVal($organization, [0, 'id'], ''),
            'published_to_registry' => getVal($organization, [0, 'published_to_registry'], false),
            'org_slug'              => getVal($organization, [0, 'reporting_org', 0, 'reporting_organization_identifier'], ''),
            'transaction_totals'    => $totalTransaction
        ];
    }

}
