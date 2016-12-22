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
     * @var Activity
     */
    private $activityModel;

    /**
     * Create a new command instance.
     *
     * @param PerfectViewerManager $perfectViewerManager
     * @param LoggerInterface      $logger
     * @param Activity             $activityModel
     */
    public function __construct(PerfectViewerManager $perfectViewerManager, LoggerInterface $logger, Activity $activityModel)
    {
        parent::__construct();
        $this->perfectViewerManager = $perfectViewerManager;
        $this->logger               = $logger;
        $this->activityModel = $activityModel;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $activities = $this->getActivities();
        $bar = $this->output->createProgressBar(count($activities));
        try {
            $bar->setMessage('Task is in progress...');
            foreach ($activities as $activity) {
                $this->perfectViewerManager->createSnapshot($activity);
                $bar->advance();
            }

        } catch (\Exception $exception) {
            $bar->setMessage('Exception catched.');

            $this->logger->error($exception->getMessage(), [
                'trace' => $exception->getTraceAsString()
            ]);
        }
        $bar->setMessage('Task is finished');
        $bar->finish();
    }

    protected function getActivities()
    {
//        $activityModel = app()->make(Activity::class);

        return $this->activityModel->query()->where('activity_workflow', '=', 3)->get();

//        return $activityModel->query()->where('activity_workflow', '=', 3)->where('published_to_registry', '=', 1)->get();
    }
}
