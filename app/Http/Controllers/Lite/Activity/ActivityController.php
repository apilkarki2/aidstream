<?php namespace App\Http\Controllers\Lite\Activity;

use App\Http\Requests\Request;
use App\Lite\Services\FormCreator\Activity;
use App\Http\Controllers\Lite\LiteController;
use App\Lite\Services\Activity\ActivityService;
use App\Lite\Services\Validation\ValidationService;

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
     * @var Activity
     */
    protected $activityForm;

    /**
     * @var ValidationService
     */
    protected $validation;

    /**
     * Entity type for Activity.
     */
    const ENTITY_TYPE = 'activity';

    /**
     * ActivityController constructor.
     * @param ActivityService   $activityService
     * @param Activity          $activityForm
     * @param ValidationService $validationService
     */
    public function __construct(ActivityService $activityService, Activity $activityForm, ValidationService $validationService)
    {
        $this->middleware('auth');
        $this->activityService = $activityService;
        $this->activityForm    = $activityForm;
        $this->validation      = $validationService;
    }

    /**
     * Show the list of activities for the current Organization.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $activities = $this->activityService->all();

        return view('lite.activity.index', compact('activities', 'form'));
    }

    /**
     *
     */
    public function create()
    {
        $form = $this->activityForm->form();

        return view('lite.activity.create', compact('form'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $rawData = $request->all();
        $version = session('version');

        if (!$this->validation->passes($rawData, self::ENTITY_TYPE, $version)) {
            return redirect()->back()->with('errors', $this->validation->errors())->withInput($rawData);
        }

        if (!($activity = $this->activityService->store($rawData, $version))) {
            return redirect()->route('lite.activity.index')->with('errors', 'Activity could not be saved.');
        }

        return redirect()->route('lite.activity.show', [$activity->id])->with('message', 'Activity saved successfully.');
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
