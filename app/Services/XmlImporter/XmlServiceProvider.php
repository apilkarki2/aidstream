<?php namespace App\Services\XmlImporter;

use DOMDocument;
use Sabre\Xml\Service;

/**
 * Class Xml
 * @package App\Services\XmlImporter
 */
class XmlServiceProvider
{
    /**
     * @var Service
     */
    protected $xmlService;

    /**
     * Xml constructor.
     * @param Service $xmlService
     */
    public function __construct(Service $xmlService)
    {
        $this->xmlService = $xmlService;
    }

    /**
     * Load xml data into an array|object|string.
     *
     * @param $data
     * @return array|object|string
     */
    public function load($data)
    {
        return $this->xmlService->parse($data);
    }

    public function version($data)
    {
        $document = new \SimpleXMLElement($data);

        return strval($document['version']);
    }
}
