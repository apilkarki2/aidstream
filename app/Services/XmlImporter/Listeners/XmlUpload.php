<?php namespace App\Services\XmlImporter\Listeners;

use App\Services\XmlImporter\Events\XmlWasUploaded;
use App\Services\XmlImporter\Foundation\Queue\ImportXml;
use App\Services\XmlImporter\Foundation\XmlQueueProcessor;
use App\Services\XmlImporter\XmlImportManager;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class XmlUpload
 * @package App\Services\XmlImporter\Listeners
 */
class XmlUpload
{
    use DispatchesJobs;

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
//        $xmlImportManager = app()->make(XmlImportManager::class);
        $userId = auth()->user()->id;
//        $this->dispatch(new ImportXml(session('org_id'), $userId, $event->filename));
        $xmlImportQueue = app()->make(XmlQueueProcessor::class);

        $xmlImportQueue->import($event->filename, session('org_id'), $userId);

//        $this->xmlImportManager->import($event->filename, session('org_id'));

        return true;
    }
}