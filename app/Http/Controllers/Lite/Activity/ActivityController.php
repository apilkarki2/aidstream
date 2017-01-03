<?php namespace App\Http\Controllers\Lite\Activity;

use App\Http\Controllers\Lite\LiteController;
use App\Lite\Services\FormCreator\Activity;
use App\Http\Requests\Request;
use App\Lite\Services\Activity\ActivityService;
use App\Lite\Services\Validation\ValidationService;
use App\Lite\Traits\ProvidesValidationRules;
use Illuminate\Contracts\Validation\Factory;

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

    protected $validation;

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
        if (!$this->validation->passes($request->all(), self::ENTITY_TYPE, session('version'))) {
            return redirect()->back()->with('errors', $this->validation->errors());
        }

        $this->activityService->store();
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
