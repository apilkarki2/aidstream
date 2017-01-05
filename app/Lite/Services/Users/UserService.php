<?php namespace App\Lite\Services\Users;


use App\Lite\Contracts\UserRepositoryInterface;
use App\Lite\Repositories\Users\UserRepository;
use App\Lite\Services\Traits\ProvidesLoggerContext;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class UserService
 * @package App\Lite\Services\Users
 */
class UserService
{
    use ProvidesLoggerContext;

    /**
     * @var UserRepository
     */
    protected $userRepository;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * UserService constructor.
     * @param UserRepositoryInterface $userRepository
     * @param LoggerInterface         $logger
     */
    public function __construct(UserRepositoryInterface $userRepository, LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;
        $this->logger         = $logger;
    }

    /**
     * Save the user details.
     *
     * @param array $user
     * @return boolean
     */
    public function save(array $user)
    {
        try {
            $user = $this->userRepository->save($user);

            $this->logger->info('User has been created successfully.', $this->getContext());

            return $user;
        } catch (Exception $exception) {
            $this->logger->error(
                sprintf('Error due to %s', $exception->getMessage()),
                $this->getContext($exception)
            );

            return false;
        }
    }

    /**
     * Return all the users present in the organisation.
     *
     * @param $orgId
     * @return mixed
     */
    public function all($orgId)
    {
        return $this->userRepository->all($orgId);
    }

    /**
     * Returns the specific user.
     *
     * @param $userId
     * @return mixed
     */
    public function find($userId)
    {
        return $this->userRepository->find($userId);
    }

    /**
     * Delete the user.
     *
     * @param $userId
     * @return bool
     */
    public function delete($userId)
    {
        try {
            $user = $this->userRepository->delete($userId);

            $this->logger->info('User has been deleted successfully.', $this->getContext());

            return $user;
        } catch (Exception $exception) {
            $this->logger->error(
                sprintf('Error due to %s', $exception->getMessage()),
                [
                    'user'     => auth()->user()->id,
                    'userName' => auth()->user()->getNameAttribute,
                    'trace'    => $exception->getTraceAsString()
                ]
            );

            return false;
        }
    }

    /**
     * Update the permission of the user.
     *
     * @param $userId
     * @param $permission
     * @return bool
     */
    public function updatePermission($userId, $permission)
    {
        try {
            $this->userRepository->update($userId, ['role_id' => $permission]);

            $this->logger->info('User Permission has been updated successfully.', $this->getContext());

            return true;

        } catch (Exception $exception) {
            $this->logger->error(
                sprintf('Error due to %s', $exception->getMessage()),
                [
                    'user'     => auth()->user()->id,
                    'userName' => auth()->user()->getNameAttribute,
                    'trace'    => $exception->getTraceAsString()
                ]
            );

            return false;
        }
    }
}

