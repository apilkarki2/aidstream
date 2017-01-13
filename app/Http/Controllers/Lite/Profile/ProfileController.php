<?php namespace App\Http\Controllers\Lite\Profile;

use App\Http\Controllers\Lite\LiteController;
use App\Http\Requests\Request;
use App\Lite\Services\Profile\ProfileService;
use App\Lite\Services\Validation\ValidationService;
use Illuminate\Http\RedirectResponse;
use Kris\LaravelFormBuilder\FormBuilder;

/**
 * Class ProfileController
 * @package App\Http\Controllers\Lite\Profile
 */
class ProfileController extends LiteController
{
    /**
     * @var FormBuilder
     */
    protected $formBuilder;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * @var ProfileService
     */
    protected $profileService;

    /**
     * ProfileController constructor.
     * @param FormBuilder       $formBuilder
     * @param ProfileService    $profileService
     * @param ValidationService $validationService
     */
    public function __construct(FormBuilder $formBuilder, ProfileService $profileService, ValidationService $validationService)
    {
        $this->middleware('auth');
        $this->profileService    = $profileService;
        $this->formBuilder       = $formBuilder;
        $this->validationService = $validationService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $orgId        = session('org_id');
        $organisation = $this->profileService->getOrg($orgId);

        return view('lite.profile.index', compact('organisation'));
    }

    /**
     * Provides Profile form with models
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editProfile()
    {
        $orgId   = session('org_id');
        $userId  = auth()->user()->id;
        $version = session('version');

        $model = $this->profileService->getFormModel($userId, $orgId, $version);
        $form  = $this->formBuilder->create(
            'App\Lite\Forms\V202\Profile',
            [
                'method' => 'PUT',
                'model'  => $model,
                'url'    => route('lite.user.profile.store')
            ]
        );

        return view('lite.profile.editProfile', compact('form'));
    }

    /**
     * Stores the Profile value
     *
     * @param Request $request
     * @return ProfileController|\Illuminate\Http\RedirectResponse
     */
    public function storeProfile(Request $request)
    {
        $userId  = auth()->user()->id;
        $orgId   = session('org_id');
        $rawData = $request->all();
        $version = session('version');

        if (!$this->validationService->passes($rawData, 'Profile', $version)) {
            return redirect()->back()->with('errors', $this->validationService->errors())->withInput($rawData);
        }

        if ($this->profileService->store($orgId, $userId, $rawData, $version)) {
            return redirect()->route('lite.user.profile.index')->withResponse(['type' => 'success', 'messages' => [trans('lite/profile.profile_saved_successfully')]]);
        }

        return redirect()->back()->withResponse(['type' => 'danger', 'messages' => [trans('lite/profile.failed_to_save_profile')]]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editPassword()
    {
        $form = $this->formBuilder->create(
            'App\Lite\Forms\V202\Password',
            [
                'method' => 'PUT',
                'url'    => route('lite.user.password.store')
            ]
        );

        return view('lite.profile.editPassword', compact('form'));
    }

    /**
     * Stores changed password
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function storePassword(Request $request)
    {
        $user    = auth()->user();
        $rawData = $request->all();
        $version = session('version');

        if (!$this->validationService->passes($rawData, 'Password', $version)) {
            return redirect()->back()->with('errors', $this->validationService->errors())->withInput($rawData);
        }

        if ($this->profileService->storePassword($user, $rawData)) {
            return redirect()->route('lite.user.profile.index')->withResponse(['type' => 'success', 'messages' => [trans('lite/profile.profile_saved_successfully')]]);
        }

        return redirect()->back()->withResponse(['type' => 'danger', 'messages' => [trans('lite/profile.new_password_mismatched')]]);
    }
}
