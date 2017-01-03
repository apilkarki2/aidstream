<?php namespace App\Lite\Contracts;

use App\Models\Activity\Activity;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface ActivityRepositoryInterface
 * @package App\Lite\Contracts
 */
interface ActivityRepositoryInterface
{
    /**
     * Get all the activities of the current Organization.
     *
     * @param $organizationId
     * @return Collection
     */
    public function all($organizationId);

    /**
     * Find an Activity by its id.
     *
     * @param $id
     * @return Activity
     */
    public function find($id);
}
