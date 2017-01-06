<?php namespace App\Lite\Services\Data\V202\Activity;

use App\Lite\Services\Data\Contract\MapperInterface;

/**
 * Class ActivityData
 * @package App\Lite\Services\Data\Activity
 */
class Activity implements MapperInterface
{
    /**
     * Raw data holder for Activity entity.
     *
     * @var array
     */
    protected $rawData = [];

    /**
     * Data template for Activity.
     *
     * @var array
     */
    protected $template = [];

    /**
     * ActivityData constructor.
     * @param array $rawData
     */
    public function __construct(array $rawData)
    {
        $this->rawData = $rawData;
    }

    /**
     * {@inheritdoc}
     */
    public function map()
    {
        // TODO:: Mapping for Activity Data.
        return $this->rawData;
    }
}
