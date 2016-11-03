<?php namespace App\Services\XmlImporter\XmlImportManager;

use App\Services\XmlImporter\XmlServiceProvider;
use App\Services\XmlImporter\Mapper\Xml as XmlMapper;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class XmlImportManager
 * @package App\Services\XmlImporter\XmlImportManager
 */
class XmlImportManager
{
    /**
     * @var XmlServiceProvider
     */
    protected $xmlServiceProvider;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var XmlMapper
     */
    protected $xmlMapper;

    /**
     * XmlImportManager constructor.
     *
     * @param XmlServiceProvider $xmlServiceProvider
     * @param XmlMapper          $xmlMapper
     * @param LoggerInterface    $logger
     */
    public function __construct(XmlServiceProvider $xmlServiceProvider, XmlMapper $xmlMapper, LoggerInterface $logger)
    {
        $this->xmlServiceProvider = $xmlServiceProvider;
        $this->logger             = $logger;
        $this->xmlMapper          = $xmlMapper;
    }

    /**
     * Import the Xml data.
     *
     * @param UploadedFile $file
     * @return bool|null
     */
    public function import(UploadedFile $file)
    {
        try {
            $contents = file_get_contents($file);
            $version  = $this->xmlServiceProvider->version($contents);
            $xmlData  = $this->xmlServiceProvider->load($contents);

            $this->xmlMapper->map($xmlData, $version);

            dd($xmlData);

            return true;
        } catch (Exception $exception) {
            dd($exception);
            $this->logger->error($exception->getMessage());

            return null;
        }

    }
}
