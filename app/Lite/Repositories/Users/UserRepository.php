<?php namespace App\Lite\Repositories\Users;


use App\Lite\Contracts\UserRepositoryInterface;
use App\User;

/**
 * Class UserRepository
 *
 * @package App\Lite\Repositories\Users
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * @var User
     */
    protected $user;

    /**
     * UserRepository constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function find($userId)
    {
        return $this->user->findOrFail($userId);
    }

    public function save(array $user)
    {
        $user['verified'] = true;
        $user['password'] = bcrypt($user['password']);
        $user['org_id']   = session('org_id');

        return $this->user->create($user);
    }

    public function all($orgId)
    {
        return $this->user->where('org_id', $orgId)->get();
    }

    public function update($userId, array $parameters)
    {
        $user = $this->find($userId);

        foreach ($parameters as $key => $value) {
            $user->{$key} = $value;
        }

        return $user->save();
    }

    /**
     * @param $userId
     * @return bool
     */
    public function delete($userId)
    {
        $user = $this->find($userId);

        return $user->delete();
    }
}

