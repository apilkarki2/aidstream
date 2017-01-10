<?php namespace App\Http\Controllers\Lite\Profile;

use App\Http\Controllers\Lite\LiteController;
use App\Http\Requests\Request;
use App\Lite\Services\Profile\ProfileService;
use App\Lite\Services\Validation\ValidationService;
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
    private $profileService;

    /**
     * ProfileController constructor.
     * @param FormBuilder    $formBuilder
     * @param ProfileService $profileService
     */
    public function __construct(FormBuilder $formBuilder, ProfileService $profileService)
    {
        $this->middleware('auth');
        $this->profileService = $profileService;
        $this->formBuilder = $formBuilder;
    }

    public function index()
    {
        $orgId = auth()->user()->org_id;

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
        $orgId = auth()->user()->org_id;
        $userId = auth()->user()->id;

        $organisation = $this->profileService->getOrg($orgId);
        $user = $this->profileService->getUser($userId);

        $model = array_merge($organisation->toArray(), $user->toArray());

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
        /*$rawData = $request->all();
        $version = session('version');
        if (!$this->validationService->passes($rawData, 'Profile', $version)) {
            return redirect()->back()->with('errors', $this->validationService->errors())->withInput($rawData);
        }

        if ($this->ProfileService->store($request->all())) {
            return redirect()->route('lite.Profile.edit')->withResponse(['type' => 'success', 'messages' => ['Profile saved successfully.']]);
        }

        return redirect()->route('lite.Profile.edit')->withResponse(['type' => 'danger', 'messages' => ['Error occurred during saving.']]);
    */
    }

    public function editUsername()
    {

    }

    public function storeUsername()
    {

    }

    public function editPassword()
    {

    }

    public function storePassword()
    {

    }
}
