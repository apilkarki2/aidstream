<?php

namespace App\Console\Commands;

use App\Models\Activity\Activity;
use App\Models\ActivityPublished;
use App\Models\Organization\Organization;
use App\Models\PerfectViewer\ActivitySnapshot;
use Illuminate\Console\Command;

class UpdatePublishedIati extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:publishediati';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the published to registry column of activities';
    /**
     * @var ActivityPublished
     */
    private $activityPublished;
    /**
     * @var Activity
     */
    private $activity;
    /**
     * @var ActivitySnapshot
     */
    private $activitySnapshot;

    /**
     * Create a new command instance.
     *
     * @param ActivityPublished    $activityPublished
     * @param Activity             $activity
     * @param ActivitySnapshot     $activitySnapshot
     */
    public function __construct(ActivityPublished $activityPublished, Activity $activity, ActivitySnapshot $activitySnapshot)
    {
        parent::__construct();
        $this->activityPublished = $activityPublished;
        $this->activity          = $activity;
        $this->activitySnapshot = $activitySnapshot;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $activityId = $this->getActivityId();
        $this->updateActivities($activityId);
        $this->updateActivitySnapshotPublishedStatus($activityId);
    }

    protected function getActivityId()
    {
        $activityId           = [];
        $published_activities = $this->activityPublished->whereNotNull('published_activities')->where(['published_to_register' => 1])->get();
        foreach ($published_activities as $index => $published_activity) {
            $files = $published_activity->published_activities;
            foreach ($files as $i => $file) {
                $activityId[] = last(explode('-', head(explode('.', $file))));
            }
        }

        return $activityId;
    }

    protected function updateActivities($activityId)
    {
        foreach ($activityId as $index => $id) {
            $this->activity->where('id', $id)->update(['published_to_registry' => 1]);
            dump('activity of id ' . $id . ' updated');
        }
    }

    protected function updateActivitySnapshotPublishedStatus($activityId)
    {
       foreach($activityId as $index => $id){
           $this->activitySnapshot->where('activity_id', $id)->update(['activity_in_registry' => true]);
           dump('activity_snapshot of activity id ' . $id . ' updated');
       }
    }
}
