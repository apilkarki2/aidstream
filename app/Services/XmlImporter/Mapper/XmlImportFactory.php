<?php namespace App\Services\XmlImporter\Mapper;

use App\Services\XmlImporter\Mapper\V103\Xml as V103XmlMapper;
use App\Services\XmlImporter\Mapper\V201\Xml as V201XmlMapper;

trait XmlImportFactory
{
    protected $bindings = [
        '1.03' => V103XmlMapper::class,
        '2.01' => V201XmlMapper::class,
        '2.02' => V103XmlMapper::class
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
