<?php namespace App\Services\XmlImporter;

use App\Core\V201\Repositories\Activity\ActivityRepository;
use App\Core\V201\Repositories\Activity\Result;
use App\Core\V201\Repositories\Activity\Transaction;
use App\Core\V201\Repositories\Document;
use App\Services\XmlImporter\Events\XmlWasUploaded;
use Exception;
use Illuminate\Filesystem\Filesystem;
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

    protected $filesystem;

    /**
     * XmlImportManager constructor.
     *
     * @param XmlServiceProvider $xmlServiceProvider
     * @param XmlProcessor       $xmlProcessor
     * @param SessionManager     $sessionManager
     * @param LoggerInterface    $logger
     * @param Filesystem         $filesystem
     */
    public function __construct(
        XmlServiceProvider $xmlServiceProvider,
        XmlProcessor $xmlProcessor,
        SessionManager $sessionManager,
        LoggerInterface $logger,
        Filesystem $filesystem
    ) {
        $this->xmlServiceProvider = $xmlServiceProvider;
        $this->xmlProcessor       = $xmlProcessor;
        $this->sessionManager     = $sessionManager;
        $this->logger             = $logger;
        $this->filesystem         = $filesystem;
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
            shell_exec(sprintf('chmod 777 -R %s', $this->temporaryXmlStorage()));
            $file->move($this->temporaryXmlStorage(), $file->getClientOriginalName());

            return true;
        } catch (Exception $exception) {
            $this->logger->error(
                sprintf('Error uploading Xml file due to %s', $exception->getMessage()),
                [
                    'trace' => $exception->getTraceAsString(),
                    'user'  => auth()->user()->id
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
            return sprintf('%s/%s', storage_path(sprintf('%s/%s/%s', self::UPLOADED_XML_STORAGE_PATH, session('org_id'), auth()->user()->id)), $filename);
        }

        return storage_path(sprintf('%s/%s/%s/', self::UPLOADED_XML_STORAGE_PATH, session('org_id'), auth()->user()->id));
    }

    /**
     * Get the id for the current user.
     *
     * @return mixed
     */
    protected function getUserId()
    {
        if (auth()->check()) {
            return auth()->user()->id;
        }
    }

    public function startImport($filename, $userId, $organizationId)
    {
//        $this->sessionManager->put(['xml-importing' => true]);
        $this->fireXmlUploadEvent($filename, $userId, $organizationId);
    }

    /**
     * Fire the XmlWasUploaded event.
     *
     * @param $filename
     * @param $userId
     * @param $organizationId
     */
    protected function fireXmlUploadEvent($filename, $userId, $organizationId)
    {
        Event::fire(new XmlWasUploaded($filename, $userId, $organizationId));
    }

    /**
     * Load a json file with a specific filename.
     *
     * @param $filename
     * @return mixed|null
     */
    public function loadJsonFile($filename)
    {
        $filePath = $this->temporaryXmlStorage($filename);
        try {
            return json_decode(file_get_contents($filePath), true);
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Remove Temporarily Stored Xml file.
     */
    public function removeTemporaryXmlFolder()
    {
        $filePath = $this->temporaryXmlStorage();
        $this->filesystem->deleteDirectory($filePath);
    }

}
