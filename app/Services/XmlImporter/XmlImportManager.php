<?php namespace App\Services\XmlImporter\XmlImportManager;

use App\Services\XmlImporter\XmlServiceProvider;
use App\Services\XmlImporter\Mapper\XmlProcessor;
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
     * @var XmlProcessor
     */
    protected $xmlProcessor;

    /**
     * XmlImportManager constructor.
     *
     * @param XmlServiceProvider $xmlServiceProvider
     * @param XmlProcessor       $xmlProcessor
     * @param LoggerInterface    $logger
     */
    public function __construct(XmlServiceProvider $xmlServiceProvider, XmlProcessor $xmlProcessor, LoggerInterface $logger)
    {
        $this->xmlServiceProvider = $xmlServiceProvider;
        $this->logger             = $logger;
        $this->xmlProcessor       = $xmlProcessor;
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

            $this->xmlProcessor->process($xmlData, $version);

            dd($xmlData);

            return true;
        } catch (Exception $exception) {
            dd($exception);
            $this->logger->error($exception->getMessage());

            return null;
        }

    }
}
