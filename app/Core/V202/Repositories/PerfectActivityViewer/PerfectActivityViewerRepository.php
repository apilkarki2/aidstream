<?php namespace App\Core\V202\Repositories\PerfectActivityViewer;

use App\Models\Activity\Transaction;
use App\Models\ActivityPublished;
use App\Models\PerfectActivity\ActivitySnapshot;
use Illuminate\Support\Facades\DB;

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
     * PerfectActivityViewer constructor.
     * @param ActivitySnapshot  $activitySnapshot
     * @param ActivityPublished $activityPublished
     * @param Transaction       $transaction
     */
    public function __construct(ActivitySnapshot $activitySnapshot, ActivityPublished $activityPublished, Transaction $transaction)
    {
        $this->activitySnapshot = $activitySnapshot;
        $this->activityPublished = $activityPublished;
        $this->transaction = $transaction;
    }

    /**
     * @param array $data
     * @return ActivitySnapshot
     */
    public function store(array $data)
    {
        return $this->activitySnapshot->updateOrCreate(['activity_id' => $data['activity_id'], 'org_id' => $data['org_id']], $data);
    }

    /**
     * @param $orgId
     * @return mixed
     */
    public function getPublishedFileName($orgId)
    {
        return $this->activityPublished->where('organization_id', $orgId)->orderBy('created_at', 'desc')->first(['filename']);
    }

    public function getTransactions($activityId)
    {
        $transactions = $this->transaction->where('activity_id', $activityId)->get();

        return $transactions->toArray();
    }

}