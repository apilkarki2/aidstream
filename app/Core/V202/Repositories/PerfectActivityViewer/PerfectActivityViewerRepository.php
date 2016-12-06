<?php namespace App\Core\V202\Repositories\PerfectActivityViewer;

use App\Models\Activity\Transaction;
use App\Models\ActivityPublished;
use App\Models\Organization\Organization;
use App\Models\PerfectActivity\ActivitySnapshot;

/**
 * Class PerfectActivityViewerRepository
 * @package App\Core\V202\Repositories\PerfectActivityViewer
 */
class PerfectActivityViewerRepository
{

    /**
     * @var ActivitySnapshot
     */
    protected $activitySnapshot;

    /**
     * @var ActivityPublished
     */
    protected $activityPublished;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @var Organization
     */
    protected $organization;

    /**
     * PerfectActivityViewer constructor.
     * @param ActivitySnapshot  $activitySnapshot
     * @param ActivityPublished $activityPublished
     * @param Transaction       $transaction
     * @param Organization      $organization
     */
    public function __construct(ActivitySnapshot $activitySnapshot, ActivityPublished $activityPublished, Transaction $transaction, Organization $organization)
    {
        $this->activitySnapshot  = $activitySnapshot;
        $this->activityPublished = $activityPublished;
        $this->transaction       = $transaction;
        $this->organization      = $organization;

    }

    /**
     * Create new snapshot record or updates if record already exists
     * @param array $data
     * @return ActivitySnapshot
     */
    public function store(array $data)
    {
        return $this->activitySnapshot->updateOrCreate(['activity_id' => $data['activity_id'], 'org_id' => $data['org_id']], $data);
    }

    /**
     * Provide Published Filename from organization id
     * @param $orgId
     * @return mixed
     */
    public function getPublishedFileName($orgId)
    {
        return $this->activityPublished->where('organization_id', $orgId)->orderBy('created_at', 'desc')->first(['filename']);
    }

    /**
     * Provide transaction data form activity id
     * @param $activityId
     * @return mixed
     */
    public function getTransactions($activityId)
    {
        $transactions = $this->transaction->where('activity_id', $activityId)->get();

        return $transactions->toArray();
    }

    /**
     * Provide organization data from organization id
     * @param $orgId
     * @return mixed
     */
    public function getOrganization($orgId)
    {
        return $this->organization->where('id', $orgId)->get();
    }

    /**
     * Provides all the organizations from Activity Snapshot
     * @return Organization
     */
    public function organizationQueryBuilder()
    {
        return $this->organization
            ->select('organizations.*')
            ->join('activity_snapshots', 'organizations.id', '=', 'activity_snapshots.org_id')
            ->groupBy('organizations.id');
    }

    public function getSnapshot($orgId)
    {
        return $this->activitySnapshot->where('org_id', $orgId)->get();
    }
}