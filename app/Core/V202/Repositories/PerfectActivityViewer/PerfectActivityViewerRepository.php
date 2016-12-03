<?php namespace App\Core\V202\Repositories\PerfectActivityViewer;

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
     * PerfectActivityViewer constructor.
     * @param ActivitySnapshot $activitySnapshot
     */
    public function __construct(ActivitySnapshot $activitySnapshot)
    {
        $this->activitySnapshot = $activitySnapshot;
    }

    /**
     * @param array $data
     * @return ActivitySnapshot
     */
    public function store(array $data)
    {
        return $this->activitySnapshot->create($data);
    }

    /**
     * @param $orgId
     * @return mixed
     */
    public function getPublishedFileName($orgId)
    {
        return DB::table('activity_published')->where('organization_id', $orgId)->orderBy('created_at', 'desc')->first(['filename']);
    }

    public function getTransactions($activityId)
    {
        return DB::table('activity_transactions')->where('activity_id', $activityId)->get();
    }

}