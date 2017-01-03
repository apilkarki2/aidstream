<?php namespace App\Lite\Services\Activity;

use Exception;
use Psr\Log\LoggerInterface;
use App\Lite\Contracts\ActivityRepositoryInterface;

/**
 * Class ActivityService
 * @package app\Lite\Services\Activity
 */
class ActivityService
{
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
                [
                    'user'     => auth()->user()->id,
                    'userName' => auth()->user()->getNameAttribute,
                    'trace'    => $exception->getTraceAsString()
                ]
            );

            return array();
        }
    }


}
