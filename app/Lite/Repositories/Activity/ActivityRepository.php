<?php namespace App\Lite\Repositories\Activity;

use App\Lite\Contracts\ActivityRepositoryInterface;
use App\Models\Activity\Activity;

/**
 * Class ActivityRepository
 * @package App\Lite\Repositories\Activity
 */
class ActivityRepository implements ActivityRepositoryInterface
{
    /**
     * @var Activity
     */
    protected $activity;

    /**
     * ActivityRepository constructor.
     * @param Activity $activity
     */
    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    /**
     * {@inheritdoc}
     */
    public function all($organizationId)
    {
        return $this->activity->where('organization_id', '=', $organizationId)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->activity->findOrFail($id);
    }
}
