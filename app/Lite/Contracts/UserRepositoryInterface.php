<?php namespace App\Lite\Contracts;


/**
 * Interface UserRepositoryInterface
 * @package App\Lite\Contracts
 */
interface UserRepositoryInterface
{
    /**
     * Find the details of the user.
     *
     * @param $userId
     * @return mixed
     */
    public function find($userId);

    /**
     * Save the details of the user in database.
     *
     * @param array $user
     * @return mixed
     */
    public function save(array $user);

    /**
     * Returns all the users registered for the given organisation.
     *
     * @param $orgId
     * @return mixed
     */
    public function all($orgId);

    /**
     * Delete the record of the user from database.
     *
     * @param $userId
     * @return mixed
     */
    public function delete($userId);

    /**
     * Update the details of the user from the database.
     *
     * @param       $userId
     * @param array $parameters
     * @return mixed
     */
    public function update($userId, array $parameters);
}
