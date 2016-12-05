<?php namespace App\Services\PerfectActivityViewer;

use App\Core\V202\Repositories\PerfectActivityViewer\PerfectActivityViewerRepository;
use App\Models\Activity\Activity;
use App\Models\Activity\Transaction;
use App\Models\Organization\Organization;
use Exception;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\DatabaseManager;
use Illuminate\Contracts\Logging\Log as Logger;

/**
 * Class PerfectActivityViewerManager
 * @package App\Services\PerfectActivityViewer
 */
class PerfectActivityViewerManager
{

    /**
     * @var PerfectActivityViewerRepository
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
     * @var Organization
     */
    protected $organization;

    /**
     * @var
     */
    protected $published;

    /**
     * PerfectActivityViewerManager constructor.
     * @param Guard                           $auth
     * @param PerfectActivityViewerRepository $perfectActivityViewerRepository
     * @param DatabaseManager                 $databaseManager
     * @param Logger                          $logger
     * @param Organization                    $organization
     */
    public function __construct(
        Guard $auth,
        PerfectActivityViewerRepository $perfectActivityViewerRepository,
        DatabaseManager $databaseManager,
        Logger $logger,
        Organization $organization
    ) {
        $this->auth                      = $auth;
        $this->perfectActivityViewerRepo = $perfectActivityViewerRepository;
        $this->database                  = $databaseManager;
        $this->logger                    = $logger;
        $this->organization              = $organization;
    }

    /**
     * Creates snapshot for Perfect Activity Viewer
     * @param $activity
     * @return \App\Models\PerfectActivity\ActivitySnapshot|bool
     */
    public function createSnapshot(Activity $activity)
    {
        $orgId                 = $activity->organization_id;
        $activityId            = $activity->id;
        $published_to_registry = $activity->published_to_registry;
        $organization          = $this->getReportingOrg();
        $reporting_org         = getVal($organization, [0], [])->reporting_org;
        $filename              = $this->perfectActivityViewerRepo->getPublishedFileName(getVal((array) $organization[0], ['id'], []));
        $filename              = $filename->filename;

        $transactions = $this->perfectActivityViewerRepo->getTransactions($activityId);

        $totalBudget = $this->calculateBudget($activity->budget);

        try {
            $perfectData = $this->convertIntoJson($activity, $reporting_org, $transactions, $totalBudget);

            $this->database->beginTransaction();
            $result = $this->perfectActivityViewerRepo->store($this->transformToSchema($perfectData, $orgId, $activityId, $published_to_registry, $filename));
            $this->database->commit();

            $this->logger->info(
                'Activity snapshot has been added',
                [
                    ' of activity '      => $activityId,
                    ' and organization ' => $orgId
                ]
            );

            return $result;

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
            'totalBudget'                => $totalBudget
        ];
    }

    /**
     * Provides reporting organization
     */
    private function getReportingOrg()
    {
        return $this->organization->getOrganization();
    }

    /**
     * Calculates total budget of an activity
     * @param $budget
     * @return int|string
     */
    private function calculateBudget($budget)
    {
        $totalBudget = 0;
        foreach($budget as $index => $value)
        {
            $totalBudget += getVal($value, ['value', 0, 'amount'], '');
        }
        return $totalBudget;
    }
}