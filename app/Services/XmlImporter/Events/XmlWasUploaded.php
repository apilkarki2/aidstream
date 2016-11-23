<?php namespace App\Services\XmlImporter\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

/**
 * Class XmlWasUploaded
 * @package App\Services\XmlImporter\Events
 */
class XmlWasUploaded extends Event
{
    use SerializesModels;

    /**
     * @var
     */
    public $filename;

    /**
     * XmlWasUploaded constructor.
     * @param $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}