<?php namespace App\Services\XmlImporter\Foundation\Support\Factory;

use App\Services\XmlImporter\Foundation\Mapper\Version\V1\XmlMapper as V1XmlMapper;
use App\Services\XmlImporter\Foundation\Mapper\Version\V2\XmlMapper as V2XmlMapper;


/**
 * Class XmlImportFactory
 * @package App\Services\XmlImporter\Foundation\Support\Factory
 */
trait Mapper
{
    /**
     * Mapper bindings according to the Xml Version.
     *
     * @var array
     */
    protected $bindings = [
        '1.03' => V1XmlMapper::class,
        '2.01' => V2XmlMapper::class,
        '2.02' => V2XmlMapper::class
    ];

    /**
     * Initialize Mapper class instance according to the Xml Version.
     *
     * @param $version
     * @return mixed
     */
    public function initializeMapper($version)
    {
        return $this->getMapping($version);
    }

    /**
     * Get the mapping for a specific version.
     *
     * @param $version
     * @return mixed
     */
    public function getMapping($version)
    {
        return app()->make($this->bindings[$version]);
    }
}
