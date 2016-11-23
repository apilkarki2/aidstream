<?php namespace App\Services\XmlImporter\Listeners;

use App\Services\XmlImporter\Events\XmlWasUploaded;
use App\Services\XmlImporter\XmlImportManager;

/**
 * Class XmlUpload
 * @package App\Services\XmlImporter\Listeners
 */
class XmlUpload
{
    /**
     * @var XmlImportManager
     */
    protected $xmlImportManager;

    /**
     * XmlUpload constructor.
     * @param XmlImportManager $xmlImportManager
     */
    public function __construct(XmlImportManager $xmlImportManager)
    {
        $this->xmlImportManager = $xmlImportManager;
    }

    /**
     * Handle the XmlWasUploadedEvent.
     *
     * @param XmlWasUploaded $event
     * @return bool
     */
    public function handle(XmlWasUploaded $event)
    {
        $this->xmlImportManager->import($event->filename);

        return true;
    }
}