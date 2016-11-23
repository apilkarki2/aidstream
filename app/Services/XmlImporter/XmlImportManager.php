<?php namespace App\Services\XmlImporter;

use App\Services\XmlImporter\Events\XmlWasUploaded;
use Exception;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Event;
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
    const UPLOADED_XML_STORAGE_PATH = 'xmlImporter/tmp/file';

    /**
     * @var XmlServiceProvider
     */
    protected $xmlServiceProvider;

    /**
     * @var XmlProcessor
     */
    protected $xmlProcessor;

    protected $sessionManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * XmlImportManager constructor.
     *
     * @param XmlServiceProvider $xmlServiceProvider
     * @param XmlProcessor       $xmlProcessor
     * @param SessionManager     $sessionManager
     * @param LoggerInterface    $logger
     */
    public function __construct(XmlServiceProvider $xmlServiceProvider, XmlProcessor $xmlProcessor, SessionManager $sessionManager, LoggerInterface $logger)
    {
        $this->xmlServiceProvider = $xmlServiceProvider;
        $this->xmlProcessor       = $xmlProcessor;
        $this->sessionManager     = $sessionManager;
        $this->logger             = $logger;
        $this->userId             = $this->getUserId();
    }

    /**
     * Temporarily store the uploaded Xml file.
     *
     * @param UploadedFile $file
     * @return bool|null
     */
    public function store(UploadedFile $file)
    {
        try {
            $file->move($this->temporaryXmlStorage(), $file->getClientOriginalName());

            return true;
        } catch (Exception $exception) {
            $this->logger->error(
                sprintf('Error uploading Xml file due to %s', $exception->getMessage()),
                [
                    'trace' => $exception->getTraceAsString(),
                    'user'  => $this->userId
                ]
            );

            return null;
        }

    }

    /**
     * Import the Xml data.
     *
     * @param $filename
     * @return bool|null
     */
    public function import($filename)
    {
        try {
            $file     = $this->temporaryXmlStorage($filename);
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

    /**
     * Get the temporary storage path for the uploaded Xml file.
     *
     * @param null $filename
     * @return string
     */
    protected function temporaryXmlStorage($filename = null)
    {
        if ($filename) {
            return sprintf('%s/%s', storage_path(sprintf('%s/%s/%s', self::UPLOADED_XML_STORAGE_PATH, session('org_id'), $this->userId)), $filename);
        }

        return storage_path(sprintf('%s/%s/%s/', self::UPLOADED_XML_STORAGE_PATH, session('org_id'), $this->userId));
    }

    /**
     * Get the id for the current user.
     *
     * @return mixed
     */
    protected function getUserId()
    {
        return auth()->user()->id;
    }

    public function startImport($filename)
    {
//        $this->sessionManager->put(['xml-importing' => true]);
        $this->fireXmlUploadEvent($filename);
    }

    /**
     * Fire the XmlWasUploaded event.
     *
     * @param $filename
     */
    protected function fireXmlUploadEvent($filename)
    {
        Event::fire(new XmlWasUploaded($filename));
    }
}
