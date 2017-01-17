<?php namespace App\Http\Controllers\Lite\Activity;

use App\Http\Requests\Request;
use App\Lite\Services\FormCreator\Activity;
use App\Http\Controllers\Lite\LiteController;
use App\Lite\Services\Activity\ActivityService;
use App\Lite\Services\FormCreator\Budget;
use App\Lite\Services\Validation\ValidationService;
use Illuminate\Http\RedirectResponse;

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
    const ENTITY_TYPE = 'Activity';
    /**
     * @var Budget
     */
    protected $budgetForm;

    /**
     * ActivityController constructor.
     * @param ActivityService   $activityService
     * @param Budget            $budgetForm
     * @param Activity          $activityForm
     * @param ValidationService $validationService
     */
    public function __construct(ActivityService $activityService, Budget $budgetForm, Activity $activityForm, ValidationService $validationService)
    {
        $this->middleware('auth');
        $this->activityService = $activityService;
        $this->activityForm    = $activityForm;
        $this->validation      = $validationService;
        $this->budgetForm      = $budgetForm;
    }

    /**
     * Show the list of activities for the current Organization.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $activities = $this->activityService->all();

        return view('lite.activity.index', compact('activities', 'form'));
    }

    /**
     * Displays form to create activity.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $form = $this->activityForm->form(route('lite.activity.store'));

        return view('lite.activity.create', compact('form'));
    }

    /**
     * Save Activity to the database.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $rawData = $request->except('_token');
        $version = session('version');

        if (!$this->validation->passes($rawData, self::ENTITY_TYPE, $version)) {
            return redirect()->back()->with('errors', $this->validation->errors())->withInput($rawData);
        }

        if (!($activity = $this->activityService->store($rawData, $version))) {
            return redirect()->route('lite.activity.index')->withResponse(['type' => 'danger', 'code' => ['save_failed', ['name' => trans('lite/global.activity')]]]);
        }

        return redirect()->route('lite.activity.show', [$activity->id])->withResponse(['type' => 'success', 'code' => ['created', ['name' => trans('lite/global.activity')]]]);
    }

    /**
     * Display the detail of an activity.
     *
     * @param $activityId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($activityId)
    {
        $activity         = $this->activityService->find($activityId);
        $statusLabel      = ['draft', 'completed', 'verified', 'published'];
        $activityWorkflow = $activity->activity_workflow;
        $btn_status_label = ['Completed', 'Verified', 'Published'];
        $btn_text         = $activityWorkflow > 2 ? "" : $btn_status_label[$activityWorkflow];

        if ($activity['activity_workflow'] == 0) {
            $nextRoute = route('lite.activity.complete', $activityId);
        } elseif ($activity['activity_workflow'] == 1) {
            $nextRoute = route('lite.activity.verify', $activityId);
        } else {
            $nextRoute = route('lite.activity.publish', $activityId);
        }

        return view('lite.activity.show', compact('activity', 'statusLabel', 'activityWorkflow', 'btn_text', 'nextRoute'));
    }

    /**
     * Return form to edit an activity.
     *
     * @param $activityId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($activityId)
    {
        $version  = session('version');
        $activity = $this->activityService->edit($activityId, $version);
        $form     = $this->activityForm->form(route('lite.activity.update', $activityId), $activity);

        return view('lite.activity.create', compact('form', 'activity'));
    }


    /**
     * Delete an activity
     *
     * @param $activityId
     * @return RedirectResponse
     */
    public function destroy($activityId)
    {
        if ($this->activityService->delete($activityId)) {
            return redirect()->back()->withResponse(['type' => 'success', 'code' => ['deleted', ['name' => trans('lite/global.activity')]]]);
        }

        return redirect()->back()->withResponse(['type' => 'danger', 'code' => ['deleted_failed', ['name' => trans('lite/global.activity')]]]);
    }

    /**
     * Update an activity
     *
     * @param         $activityId
     * @param Request $request
     * @return RedirectResponse
     */
    public function update($activityId, Request $request)
    {
        $rawData = $request->except('_token');
        $version = session('version');

        if (!$this->validation->passes($rawData, self::ENTITY_TYPE, $version)) {
            return redirect()->back()->with('errors', $this->validation->errors())->withInput($rawData);
        }

        if (!$this->activityService->update($activityId, $rawData, $version)) {
            return redirect()->route('lite.activity.show', $activityId)->withResponse(['type' => 'danger', 'code' => ['save_failed', ['name' => trans('lite/global.activity')]]]);
        }

        return redirect()->route('lite.activity.show', $activityId)->withResponse(['type' => 'success', 'code' => ['created', ['name' => trans('lite/global.activity')]]]);
    }

    /**
     * Duplicate an activity
     *
     * @param $activityId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function duplicate($activityId)
    {
//        $activity = $this->activityService->find($activityId);
//        return view('lite.activity.index', compact('activity'));
    }

    /**
     * Creates budget of an activity.
     *
     * @param $activityId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createBudget($activityId)
    {
        $form = $this->budgetForm->form(route('lite.activity.budget.store', $activityId));

        return view('lite.activity.budget.edit', compact('form'));
    }

    /**
     * Edits budget of an activity.
     *
     * @param $activityId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editBudget($activityId)
    {
        $version = session('version');

        $model = $this->activityService->getBudgetModel($activityId, $version);

        $form = $this->budgetForm->form(route('lite.activity.budget.store', $activityId), $model);

        return view('lite.activity.budget.edit', compact('form'));
    }

    /**
     * Stores Budget of an activity.
     *
     * @param         $activityId
     * @param Request $request
     * @return RedirectResponse
     */
    public function storeBudget($activityId, Request $request)
    {
        $rawData = $request->except('_token');
        $version = session('version');

        if (!$this->validation->passes($rawData, 'Budget', $version)) {
            return redirect()->back()->with('errors', $this->validation->errors())->withInput($rawData);
        }

        if ($this->activityService->addBudget($activityId, $rawData, $version)) {
            return redirect()->route('lite.activity.show', $activityId)->withResponse(['type' => 'success', 'messages' => [trans('success.budget_success_created')]]);
        }

        return redirect()->back()->withResponse(['type' => 'danger', 'messages' => [trans('error.error_budget_create')]]);
    }

    /**
     * Deletes a single Budget.
     * @param         $activityId
     * @param Request $request
     * @return mixed
     */
    public function deleteBudget($activityId, Request $request)
    {
        if ($this->activityService->deleteBudget($activityId, $request)) {
            return redirect()->route('lite.activity.show', $activityId)->withResponse(['type' => 'success', 'messages' => [trans('success.budget_success_deleted')]]);
        }

        return redirect()->back()->withResponse(['type' => 'danger', 'messages' => [trans('error.error_budget_create')]]);

    }
}
