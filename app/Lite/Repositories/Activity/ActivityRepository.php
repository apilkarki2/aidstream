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
     * Returns all the activities of an organization.
     *
     * @param $organizationId
     * @return mixed
     */
    public function all($organizationId)
    {
        return $this->activity->where('organization_id', '=', $organizationId)->get();
    }

    /**
     * Find a specific activity.
     *
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->activity->findOrFail($id);
    }

    /**
     * Store the details of am activity in database.
     *
     * @param array $data
     * @return Activity
     */
    public function save(array $data)
    {
        $data['organization_id'] = session('org_id');

        return $this->activity->create($data);
    }

    /**
     * Delete the activity.
     *
     * @param $activityId
     * @return mixed
     */
    public function delete($activityId)
    {
        $activity = $this->find($activityId);

        return $activity->delete();
    }

    /**
     * Update the details of the activity.
     *
     * @param       $activityId
     * @param array $data
     * @return mixed
     */
    public function update($activityId, array  $data)
    {
        $activity                = $this->find($activityId);
        $data['organization_id'] = session('org_id');

        return $activity->update($data);
    }
}
