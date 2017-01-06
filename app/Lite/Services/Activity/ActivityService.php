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

            return array();
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
}
