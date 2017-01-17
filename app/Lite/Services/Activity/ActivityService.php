<?php namespace App\Lite\Services\Activity;

use App\Lite\Services\Data\V202\Activity\Activity;
use App\Lite\Services\Data\Traits\TransformsData;
use App\Lite\Services\Traits\ProvidesLoggerContext;
use Exception;
use Psr\Log\LoggerInterface;
use App\Lite\Contracts\ActivityRepositoryInterface;

/**
 * Class ActivityService
 * @package app\Lite\Services\Activity
 */
class ActivityService
{
    use ProvidesLoggerContext, TransformsData;

    /**
     * @var ActivityRepositoryInterface
     */
    protected $activityRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ActivityService constructor.
     * @param ActivityRepositoryInterface $activityRepository
     * @param LoggerInterface             $logger
     */
    public function __construct(ActivityRepositoryInterface $activityRepository, LoggerInterface $logger)
    {
        $this->activityRepository = $activityRepository;
        $this->logger             = $logger;
    }

    /**
     * Get all Activities for the current Organization.
     *
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    public function all()
    {
        try {
            return $this->activityRepository->all(session('org_id'));
        } catch (Exception $exception) {
            $this->logger->error(
                sprintf('Error due to %s', $exception->getMessage()),
                $this->getContext($exception)
            );

            return [];
        }
    }

    /**
     * Store the Activity data.
     *
     * @param array $rawData
     * @param       $version
     * @return \App\Models\Activity\Activity|null
     */
    public function store(array $rawData, $version)
    {
        try {
            $activity = $this->activityRepository->save($this->transform($this->getMapping($rawData, 'Activity', $version)));

            $this->logger->info('Activity successfully saved.', $this->getContext());

            return $activity;
        } catch (Exception $exception) {
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }

    /**
     *  Find a Specific Activity.
     *
     * @param $activityId
     * @return \App\Models\Activity\Activity
     */
    public function find($activityId)
    {
        return $this->activityRepository->find($activityId);
    }

    /**
     * Delete a activity.
     *
     * @param $activityId
     * @return mixed|null
     */
    public function delete($activityId)
    {
        try {
            $activity = $this->activityRepository->delete($activityId);

            $this->logger->info('Activity successfully deleted.', $this->getContext());

            return $activity;
        } catch (Exception $exception) {
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }

    /**
     * Returns reversely mapped activity data to edit.
     *
     * @param $activityId
     * @param $version
     * @return array
     */
    public function edit($activityId, $version)
    {
        $activity = $this->find($activityId)->toArray();

        return $this->transformReverse($this->getMapping($activity, 'Activity', $version));
    }

    /**
     * Update the activity data.
     *
     * @param $activityId
     * @param $rawData
     * @param $version
     * @return mixed|null
     */
    public function update($activityId, $rawData, $version)
    {
        try {
            $this->activityRepository->update($activityId, $this->transform($this->getMapping($rawData, 'Activity', $version)));
            $this->logger->info('Activity successfully updated.', $this->getContext());

            return true;
        } catch (Exception $exception) {
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }

    /**
     * Returns Budget Model in view format
     * @param $activityId
     * @param $version
     * @return array
     * @internal param $budget
     */
    public function getBudgetModel($activityId, $version)
    {
        $model = json_decode($this->activityRepository->find($activityId), true);

        $filteredModel = $this->transformReverse($this->getMapping($model, 'Budget', $version));

        return $filteredModel;
    }

    /**
     * Adds new budgets to the current activity.
     *
     * @param $activityId
     * @param $rawData
     * @param $version
     * @return bool|null
     */
    public function addBudget($activityId, $rawData, $version)
    {
        try {
            $mappedBudget = $this->transform($this->getMapping($rawData, 'Budget', $version));
            $activity     = $this->activityRepository->find($activityId)->toArray();

            foreach (getVal($mappedBudget, ['budget'], []) as $index => $value) {
                $activity['budget'][] = $value;
            }

            $this->activityRepository->update($activityId, $activity);

            $this->logger->info('Budget successfully added.', $this->getContext());

            return true;
        } catch (Exception $exception) {
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }

    /**
     * Deletes a budget from current activity.
     *
     * @param $activityId
     * @param $request
     * @return bool|null
     */
    public function deleteBudget($activityId, $request)
    {
        try {
            $activity = $this->find($activityId);
            $budget   = $activity->budget;

            unset($budget[$request->get('index')]);

            $activity->budget = array_values($budget);

            $activity->save();

            $this->logger->info('Budget transaction successfully deleted.', $this->getContext());

            return true;
        } catch (Exception $exception) {
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }
}
