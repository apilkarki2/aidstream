<?php namespace App\Services\PerfectViewer;

use App\Core\V202\Repositories\PerfectViewer\PerfectViewerRepository;
use App\Models\Activity\Activity;
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
    protected $perfectActivityViewerRepo;

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
    protected $exchangeRates;

    /**
     * PerfectActivityViewerManager constructor.
     * @param Guard                   $auth
     * @param PerfectViewerRepository $perfectActivityViewerRepository
     * @param DatabaseManager         $databaseManager
     * @param Logger                  $logger
     */
    public function __construct(
        Guard $auth,
        PerfectViewerRepository $perfectActivityViewerRepository,
        DatabaseManager $databaseManager,
        Logger $logger

    ) {
        $this->auth                      = $auth;
        $this->perfectActivityViewerRepo = $perfectActivityViewerRepository;
        $this->database                  = $databaseManager;
        $this->logger                    = $logger;
    }

    /**
     * Creates snapshot for Perfect Activity Viewer
     * @param $activity
     * @return PerfectViewerManager|bool
     */
    public function createSnapshot(Activity $activity)
    {
        $this->exchangeRates = json_decode(file_get_contents(app_path('./../public/data/exchangerates.json')), true);

        try {

            //activity data
            $this->defaultFieldValues = $activity->default_field_values;
            $orgId                    = $activity->organization_id;
            $activityId               = $activity->id;
            $published_to_registry    = $activity->published_to_registry;

            //transaction and budget
            $transactions     = $this->perfectActivityViewerRepo->getTransactions($activityId);
            $totalBudget      = $this->calculateBudget(getVal((array) $activity, ['budget'], []));
            $totalTransaction = $this->calculateTransaction($transactions);

            //organization data
            $organization  = $this->getOrg($orgId);
            $reporting_org = getVal($organization, [0, 'reporting_org'], []);
            $filename      = $this->perfectActivityViewerRepo->getPublishedFileName(getVal((array) $organization[0], ['id'], []));
            $filename      = $filename->filename;
            $perfectOrg    = $this->makePerfectOrg($organization, $totalTransaction);

            $perfectData = $this->convertIntoJson($activity, $reporting_org, $transactions, $totalBudget/*, $totalTransaction*/);

            $this->database->beginTransaction();
            $result    = $this->perfectActivityViewerRepo->storeActivity($this->transformToSchema($perfectData, $orgId, $activityId, $published_to_registry, $filename));
            $orgResult = $this->perfectActivityViewerRepo->storeOrganization($perfectOrg);
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
            $this->logger->error($exception, ['Activity_identifier' => $activity->id]);
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
    public function convertIntoJson($activity, $reporting_org, $transactions, $totalBudget/*, $totalTransaction*/)
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
            //            'totalTransaction'           => $totalTransaction
        ];
    }

    /**
     * Provides reporting organization
     * @param $orgId
     * @return
     */
    protected function getOrg($orgId)
    {
        return $this->perfectActivityViewerRepo->getOrganization($orgId)->toArray();
    }

    /**
     * Calculates total budget of an activity
     * @param $budget
     * @return int|string
     */
    protected function calculateBudget($budget)
    {
        $totalBudget = 0;
        foreach ($budget as $index => $value) {
            $totalBudget += getVal($value, ['value', 0, 'amount'], '');
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

            $value = $this->giveCorrectValue($transaction);

            switch (getVal($transaction, ['transaction', 'transaction_type', 0, 'transaction_type_code'], '')) {

                case 1:
                    $totalTransaction['total_incoming_funds'] += $value;
                    break;

                case 2:
                    $totalTransaction['total_commitments'] += $value;
                    break;

                case 3:
                    $totalTransaction['total_disbursements'] += $value;
                    break;

                case 4:
                    $totalTransaction['total_expenditures'] += $value;
                    break;

                default:
                    break;
            }
        }

        return $totalTransaction;
    }

    protected function giveCorrectValue($transaction)
    {
        $defaultCurrency = getVal($this->defaultFieldValues, ['0', 'default_currency']);
        $currency        = getVal($transaction, ['transaction', 'value', 0, 'currency'], '');
        $date            = getVal($transaction, ['transaction', 'value', 0, 'date'], '');
        $amount          = getVal($transaction, ['transaction', 'value', 0, 'amount'], '');

        if ($currency != 'USD') {
            if ($currency == '') {
                if ($defaultCurrency != 'USD') {
                    $eRate = getVal($this->exchangeRates, [sprintf('USD%s', $defaultCurrency)], 1);

                    return dd($amount / $eRate);
                } else {
                    if ($defaultCurrency == 'USD') {
                        return $amount;
                    }
                }
            } else {
                $eRate = getVal($this->exchangeRates, [sprintf('USD%s', $currency)], 1);

                return $amount / $eRate;
            }
        }

        return 0;
    }

    public function organizationQueryBuilder()
    {
        return $this->perfectActivityViewerRepo->organizationQueryBuilder();
    }

    public function getSnapshotWithOrgId($orgId)
    {
        return $this->perfectActivityViewerRepo->getSnapshot($orgId);
    }

    public function makePerfectOrg($organization, $totalTransaction)
    {
        return [
            'org_id'                => getVal($organization, [0, 'id'], ''),
            'published_to_registry' => $organization[0]['published_to_registry'],
            'org_slug'              => getVal($organization, [0, 'reporting_org', 0, 'reporting_organization_identifier'], ''),
            'transaction_totals'    => $totalTransaction
        ];
    }
}