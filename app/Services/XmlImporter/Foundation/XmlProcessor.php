<?php namespace App\Services\XmlImporter\Foundation;

use App\Services\XmlImporter\Foundation\Support\Factory\Mapper as MapperFactory;
use App\Services\XmlImporter\Foundation\Support\Providers\TemplateServiceProvider;

/**
 * Class XmlProcessor
 * @package App\Services\XmlImporter\Foundation
 */
class XmlProcessor
{
    use MapperFactory;

    /**
     * @var TemplateServiceProvider
     */
    protected $templateServiceProvider;

    /**
     * @var array
     */
    protected $transactions = [];

    /**
     * @var
     */
    protected $xmlMapper;

    /**
     * Xml constructor.
     * @param TemplateServiceProvider $templateServiceProvider
     */
    public function __construct(TemplateServiceProvider $templateServiceProvider)
    {
        $this->templateServiceProvider = $templateServiceProvider;
    }

    /**
     * @param array $xml
     * @param       $version
     */
    public function process(array $xml, $version)
    {
        $this->xmlMapper = $this->initializeMapper($version);
        $this->xmlMapper->map($xml, $this->templateServiceProvider->loadFor());

        dd($this->xmlMapper);
    }
}
