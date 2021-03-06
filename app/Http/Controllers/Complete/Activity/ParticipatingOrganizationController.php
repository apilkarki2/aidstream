<?php namespace App\Http\Controllers\Complete\Activity;

use App\Http\Controllers\Controller;
use App\Services\Activity\ParticipatingOrganizationManager;
use App\Services\Activity\ActivityManager;
use App\Services\FormCreator\Activity\ParticipatingOrganization as ParticipatingOrganizationForm;
use App\Services\RequestManager\Activity\ParticipatingOrganization as ParticipatingOrganizationRequestManager;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Class ParticipatingOrganizationController
 * @package app\Http\Controllers\Complete\Activity
 */
class ParticipatingOrganizationController extends Controller
{
    /**
     * @var ActivityManager
     */
    protected $activityManager;
    /**
     * @var ParticipatingOrganizationForm
     */
    protected $participatingOrganizationForm;
    /**
     * @var ParticipatingOrganizationManager
     */
    protected $participatingOrganizationManager;

    /**
     * @param ParticipatingOrganizationManager $participatingOrganizationManager
     * @param ParticipatingOrganizationForm    $participatingOrganizationForm
     * @param ActivityManager                  $activityManager
     */
    function __construct(
        ParticipatingOrganizationManager $participatingOrganizationManager,
        ParticipatingOrganizationForm $participatingOrganizationForm,
        ActivityManager $activityManager
    ) {
        $this->middleware('auth');
        $this->activityManager                  = $activityManager;
        $this->participatingOrganizationForm    = $participatingOrganizationForm;
        $this->participatingOrganizationManager = $participatingOrganizationManager;
    }

    /**
     * returns the activity contact info edit form
     * @param $id
     * @return \Illuminate\View\View
     */
    public function index($id)
    {
        $activityData = $this->activityManager->getActivityData($id);

        if (Gate::denies('ownership', $activityData)) {
            return redirect()->back()->withResponse($this->getNoPrivilegesMessage());
        }

        $participatingOrganization = $this->participatingOrganizationManager->getParticipatingOrganizationData($id);
        $activityData              = $this->activityManager->getActivityData($id);
        $form                      = $this->participatingOrganizationForm->editForm($participatingOrganization, $id);

        return view(
            'Activity.participatingOrganization.edit',
            compact('form', 'activityData', 'id')
        );
    }

    /**
     * updates activity participating organization
     * @param                                         $id
     * @param Request                                 $request
     * @param ParticipatingOrganizationRequestManager $participatingOrganizationRequestManager
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request, ParticipatingOrganizationRequestManager $participatingOrganizationRequestManager)
    {
        $activityData = $this->activityManager->getActivityData($id);

        if (Gate::denies('ownership', $activityData)) {
            return redirect()->back()->withResponse($this->getNoPrivilegesMessage());
        }

        $this->authorizeByRequestType($activityData, 'participating_organization');
        $participatingOrganization = $request->all();
        if (!$this->validateData($request->get('participating_organization'))) {
            $response = ['type' => 'warning', 'code' => ['participating_org', ['name' => 'participating organization']]];

            return redirect()->back()->withInput()->withResponse($response);
        }
        if ($this->participatingOrganizationManager->update($participatingOrganization, $activityData)) {
            $this->activityManager->resetActivityWorkflow($id);

            $response = ['type' => 'success', 'code' => ['updated', ['name' => trans('title.participating_organisation')]]];

            return redirect()->to(sprintf('/activity/%s', $id))->withResponse($response);
        }
        $response = ['type' => 'danger', 'code' => ['update_failed', ['name' => trans('title.participating_organisation')]]];

        return redirect()->back()->withInput()->withResponse($response);
    }

    /**
     * validate participating organization data based on roles
     * @param array $data
     * @return bool
     */
    private function validateData(array $data)
    {
        $check = false;
        foreach ($data as $participatingOrg) {
            $orgRole = $participatingOrg['organization_role'];
            if ($orgRole === "1" || $orgRole == "4") {
                $check = true;
                break;
            }
        }

        return $check;
    }
}
