<?php namespace App\Core\V201\Element\Activity;

/**
 * Class DefaultTiedStatus
 * @package app\Core\V201\Element\Activity
 */
class DefaultTiedStatus
{
    /**
     * @return default tied status form path
     */
    public function getForm()
    {
        return 'App\Core\V201\Forms\Activity\DefaultTiedStatus';
    }

    /**
     * @return default tied status repository
     */
    public function getRepository()
    {
        return App('App\Core\V201\Repositories\Activity\DefaultTiedStatus');
    }
}
