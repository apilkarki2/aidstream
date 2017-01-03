<?php namespace App\Http\Controllers\Lite\Activity;

use App\Http\Controllers\Lite\LiteController;
use App\Http\Requests\Request;
use App\Lite\Services\Activity\ActivityService;

/**
 * Class ActivityController
 * @package App\Http\Controllers\Lite\Activity
 */
class ActivityController extends LiteController
{
    /**
     * @var ActivityService
     */
    protected $activityService;

    /**
     * ActivityController constructor.
     * @param ActivityService $activityService
     */
    public function __construct(ActivityService $activityService)
    {
        $this->middleware('auth');
        $this->activityService = $activityService;
    }

    /**
     * Show the list of activities for the current Organization.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $activities = $this->activityService->all();

        return view('lite.activity.index', compact('activities'));
    }

    /**
     *
     */
    public function create()
    {
        // TODO::Render create Activity Form
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        // TODO::Store a new Activity
    }

    /**
     * @param $activityId
     */
    public function show($activityId)
    {
        // TODO::Show an Activity
    }

    /**
     * @param $activityId
     */
    public function edit($activityId)
    {
        // TODO::Render edit Activity Form
    }

    /**
     * @param $activityId
     */
    public function destroy($activityId)
    {
        dd($activityId);
        // TODO::Delete an Activity
    }

    /**
     * @param $activityId
     */
    public function duplicate($activityId)
    {
        // TODO::Make a duplicate for an Activity
    }
}
