<?php namespace App\Http\Controllers\Auth;

use App\Core\Form\BaseForm;
use App\Http\Controllers\Controller;
use App\Services\Registration;
use App\Services\RegistrationAgencies;
use App\Services\RequestManager\RegisterOrganization;
use App\Services\RequestManager\RegisterUsers;
use App\Services\Verification;

/**
 * Class RegistrationController
 * @package App\Http\Controllers\Auth
 */
class RegistrationController extends Controller
{
    /**
     * @var BaseForm
     */
    protected $baseForm;
    /**
     * @var Registration
     */
    protected $registrationManager;
    /**
     * @var Verification
     */
    protected $verificationManager;
    /**
     * @var RegistrationAgencies
     */
    private $regAgencyManager;

    /**
     * @param BaseForm             $baseForm
     * @param Registration         $registrationManager
     * @param Verification         $verificationManager
     * @param RegistrationAgencies $regAgencyManager
     */
    public function __construct(BaseForm $baseForm, Registration $registrationManager, Verification $verificationManager, RegistrationAgencies $regAgencyManager)
    {
        $this->middleware('guest', ['except' => 'getLogout']);
        $this->baseForm            = $baseForm;
        $this->registrationManager = $registrationManager;
        $this->verificationManager = $verificationManager;
        $this->regAgencyManager    = $regAgencyManager;
    }

    /**
     * returns organization registration view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showOrgForm()
    {
        $orgType      = $this->baseForm->getCodeList('OrganizationType', 'Organization', false);
        $countries    = $this->baseForm->getCodeList('Country', 'Organization', false);
        $orgRegAgency = $this->baseForm->getCodeList('OrganisationRegistrationAgency', 'Organization', false);
        $dbRegAgency  = $this->regAgencyManager->getRegAgenciesCode();
        $orgRegAgency = array_merge($orgRegAgency, $dbRegAgency);

        return view('auth.organization', compact('orgType', 'countries', 'orgRegAgency'));
    }

    /**
     * saves organization and redirect to user registration page
     * @param RegisterOrganization $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveOrganization(RegisterOrganization $request)
    {
        $orgInfo = request()->except('_token');
        session()->put('org_info', $orgInfo);

        $similarOrg = $this->registrationManager->getSimilarOrg($orgInfo['organization_name']);

        if ($similarOrg->count()) {
            return redirect()->route('registration.similar-organizations')->withInput(['organizations' => $orgInfo['organization_name']]);
        }

        return redirect()->route('registration.users');
    }

    /**
     * returns users registration view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showUsersForm()
    {
        $orgInfo = session('org_info');

        if (!$orgInfo) {
            return redirect()->route('registration.organization');
        }

        $dbRoles = \DB::table('role')->whereNotNull('permissions')->orderBy('role', 'desc')->get();
        $roles   = [];
        foreach ($dbRoles as $role) {
            $roles[$role->id] = $role->role;
        }

        return view('auth.users', compact('roles'));
    }

    /**
     * save organization info and users
     * @param RegisterUsers $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function completeRegistration(RegisterUsers $request)
    {
        $users = request()->except('_token');
        session()->put('org_users', $users);
        $orgInfo = session('org_info');

        if ($organization = $this->registrationManager->register($orgInfo, $users)) {
            return $this->postRegistration($organization);
        } else {
            $response = ['type' => 'danger', 'code' => ['failed_registration']];

            return redirect()->back()->withInput()->withResponse($response);
        }
    }

    /**
     * sends emails to users after registration
     * @param $organization
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function postRegistration($organization)
    {
        session()->forget('org_info');
        session()->forget('org_users');
        $user = $organization->users->where('role_id', 1)->first();
        $this->verificationManager->sendVerificationEmail($user);

        return redirect()->to('/auth/login')->withMessage(
            sprintf(
                'A verification email has been sent to %s. Please check your email inbox and click on the link in the email to verify your email address.',
                $user->email
            )
        );
    }

    /**
     * show similar organizations
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showSimilarOrganizations()
    {
        $organizations = $this->registrationManager->getOrganizationList();

        return view('auth.similarOrg', compact('organizations'));
    }

    /**
     * returns list of similar organizations
     * @param $orgName
     * @return array
     */
    public function listSimilarOrganizations($orgName)
    {
        $similarOrganizations = $this->registrationManager->getSimilarOrg($orgName);

        return $this->registrationManager->prepareSimilarOrg($similarOrganizations);
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function submitSimilarOrganization()
    {
        if ($adminEmail = request('similar_organization')) {
            return redirect()->to('/password/email')->withInput(['email' => $adminEmail, 'redirectedFrom' => 'registration']);
        }

        return redirect()->route('registration.users');
    }

}
