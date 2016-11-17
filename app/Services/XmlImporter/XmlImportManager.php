<?php namespace App\Services\XmlImporter;

use Exception;
use Psr\Log\LoggerInterface;
use App\Services\XmlImporter\Foundation\XmlProcessor;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Services\XmlImporter\Foundation\Support\Providers\XmlServiceProvider;

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

//            if ($this->xmlServiceProvider->isValidAgainstSchema($contents)) {
                $version = $this->xmlServiceProvider->version($contents);
                $xmlData = $this->xmlServiceProvider->load($contents);

                $this->xmlProcessor->process($xmlData, $version);

                return true;
//            }

//            return false;
        } catch (Exception $exception) {
            dd($exception);
            $this->logger->error(
                $exception->getMessage(),
                [
                    'trace' => $exception->getTraceAsString(),
                    'user'  => auth()->user()->getNameAttribute()
                ]
            );

            return null;
        }

    }
}
