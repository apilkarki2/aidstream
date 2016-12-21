<?php

namespace App\Console\Commands;

use App\Models\Activity\Activity;
use App\Services\PerfectViewer\PerfectViewerManager;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;

class CreateSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-snapshots';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $perfectViewerManager;

    protected $logger;

    /**
     * Create a new command instance.
     *
     * @param PerfectViewerManager $perfectViewerManager
     * @param LoggerInterface      $logger
     */
    public function __construct(PerfectViewerManager $perfectViewerManager, LoggerInterface $logger)
    {
        parent::__construct();
        $this->perfectViewerManager = $perfectViewerManager;
        $this->logger               = $logger;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $activities = $this->getActivities();

        try {
            foreach ($activities as $activity) {
                $this->perfectViewerManager->createSnapshot($activity);
            }

            $this->info('Snapshots created.');
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), [
                'trace' => $exception->getTraceAsString()
            ]);
        }
    }

    protected function getActivities()
    {
        $activityModel = app()->make(Activity::class);

        return $activityModel->query()->where('activity_workflow', '=', 3)->get();

//        return $activityModel->query()->where('activity_workflow', '=', 3)->where('published_to_registry', '=', 1)->get();
    }
}
