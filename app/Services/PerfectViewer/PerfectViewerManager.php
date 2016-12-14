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
    private $historicalExchangeRate;

    /**
     * PerfectActivityViewerManager constructor.
     * @param Guard                   $auth
     * @param PerfectViewerRepository $perfectViewerRepository
     * @param DatabaseManager         $databaseManager
     * @param Logger                  $logger
     * @param HistoricalExchangeRate  $historicalExchangeRate
     */
    public function __construct(
        Guard $auth,
        PerfectViewerRepository $perfectViewerRepository,
        DatabaseManager $databaseManager,
        Logger $logger,
        HistoricalExchangeRate $historicalExchangeRate
    ) {
        $this->auth                   = $auth;
        $this->perfectViewerRepo      = $perfectViewerRepository;
        $this->database               = $databaseManager;
        $this->logger                 = $logger;
        $this->historicalExchangeRate = $historicalExchangeRate;
    }

    /**
     * Creates snapshot for Perfect Activity Viewer
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
            $transactions     = $this->perfectViewerRepo->getTransactions($activityId);
            $dates            = $this->getDates($activity, $transactions);
            $newDates         = $this->getNewDates($dates);
            $newExchangeRates = $this->newExchangeRates($newDates);
            $totalBudget      = $this->calculateBudget(getVal($activity->toArray(), ['budget'], []));
            $totalTransaction = $this->calculateTransaction($transactions);

            //store new exchange rates
            $this->storeExchangeRates($newExchangeRates);

            //organization data
            $organization  = $this->getOrg($orgId);
            $reporting_org = getVal($organization, [0, 'reporting_org'], []);
            $filename      = $this->perfectViewerRepo->getPublishedFileName(getVal((array) $organization[0], ['id'], []));
            $filename      = $filename->filename;
            $perfectOrg    = $this->makePerfectOrg($organization, $totalTransaction);

            $perfectData = $this->convertIntoJson($activity, $reporting_org, $transactions, $totalBudget);

            $this->database->beginTransaction();
            $result    = $this->perfectViewerRepo->storeActivity($this->transformToSchema($perfectData, $orgId, $activityId, $published_to_registry, $filename));
            $orgResult = $this->perfectViewerRepo->storeOrganization($perfectOrg);
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
     * Converts given attributes to JSON
     * @param $activity
     * @param $reporting_org
     * @param $transactions
     * @param $totalBudget
    //     * @param $totalTransaction
     * @return array
     */
    public function convertIntoJson($activity, $reporting_org, $transactions, $totalBudget)
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
     * @param $orgId
     * @return
     */
    protected function getOrg($orgId)
    {
        return $this->perfectViewerRepo->getOrganization($orgId)->toArray();
    }

    /**
     * Calculates total budget of an activity
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

    protected function calculateTransaction($transactions)
    {
        $totalTransaction['total_incoming_funds'] = 0;
        $totalTransaction['total_commitments']    = 0;
        $totalTransaction['total_disbursements']  = 0;
        $totalTransaction['total_expenditures']   = 0;

        foreach ($transactions as $index => $transaction) {

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

        return $totalTransaction;
    }

    protected function giveCorrectValue($data)
    {
        $defaultCurrency = getVal($this->defaultFieldValues, ['0', 'default_currency']);
        $currency        = getVal($data, ['value', 0, 'currency'], '');
        $date            = getVal($data, ['value', 0, 'value_date'], '');
        $amount          = (float) getVal($data, ['value', 0, 'amount'], '');
        $dbDate          = json_decode($this->exchangeRatesBuilder->where('date', $date)->first(), true) ?: [];

        if ($currency != 'USD') {
            if ($currency == '') {
                if ($defaultCurrency != 'USD') {
                    $eRate = getVal($dbDate, ['exchange_rates', sprintf('%s', $defaultCurrency)], 1);

                    return $amount / $eRate;
                }

                return $amount;
            } else {
                $eRate = getVal($dbDate, ['exchange_rates', sprintf('%s', $currency)], 1);

                return $amount / $eRate;
            }
        }

        return $amount;
    }

    public function organizationQueryBuilder()
    {
        return $this->perfectViewerRepo->organizationQueryBuilder();
    }

    public function activityQueryBuilder()
    {
        return $this->perfectViewerRepo->activityQueryBuilder();

    }

    public function getSnapshotWithOrgId($orgId)
    {
        return $this->perfectViewerRepo->getSnapshot($orgId);
    }

    public function getOrgWithOrgId($orgId)
    {
        return $this->perfectViewerRepo->getOrgWithId($orgId);
    }

    public function makePerfectOrg($organization, $totalTransaction)
    {
        return [
            'org_id'                => getVal($organization, [0, 'id'], ''),
            'org_data'              => getVal($organization, [0], []),
            'published_to_registry' => $organization[0]['published_to_registry'],
            'org_slug'              => getVal($organization, [0, 'reporting_org', 0, 'reporting_organization_identifier'], ''),
            'transaction_totals'    => $totalTransaction
        ];
    }

    protected function getDates($activity, $transactions)
    {
        $dates = [];

        if (!is_array($activity)) {
            $activity = (array) $activity;
        }

        foreach (getVal($activity, ['budget'], []) as $budget) {
            $dates[] = getVal($budget, ['value', 0, 'value_date'], '');
        }

        foreach ($transactions as $transaction) {
            $dates[] = getVal($transaction, ['transaction', 'value', 0, 'date'], '');
        }

        return $dates;
    }

    protected function getNewDates($dates)
    {
        $allDates = $this->exchangeRatesBuilder->select('date')->get()->toArray();

        return array_values(array_diff($dates, array_flatten($allDates)));
    }

    protected function newExchangeRates($newDates)
    {
        $exchangeRates = [];
        foreach ($newDates as $index => $newDate) {
            $exchangeRates[] = $this->clean(json_decode($this->curl($newDate), true), $newDate);
        }

        return $exchangeRates;
    }

    protected function curl($date)
    {
        $ch = curl_init('http://apilayer.net/api/historical' . '?access_key=' . 'c92a72092ee24a60fc0e0cb7fd1377bf' . '&date=' . $date . '&format=1');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }


    protected function clean($json, $date)
    {
        $rates = [];

        if (!$json) {
            $json = (array) $json;
        }

        foreach (getVal($json, ['quotes'], []) as $key => $value) {
            $toCurrency = str_replace('USD', '', $key);

            if ($toCurrency !== '') {
                $rates[$date][$toCurrency] = $value;
            }
        }

        return $rates;
    }

    protected function transformExchangeRates($exchangeRate)
    {
        return [
            'date'           => key($exchangeRate),
            'exchange_rates' => value($exchangeRate)
        ];
    }

    protected function storeExchangeRates($newExchangeRates)
    {
        foreach ($newExchangeRates as $index => $rates) {
            $this->historicalExchangeRate->create($this->transformExchangeRates($rates));
        }
    }
}