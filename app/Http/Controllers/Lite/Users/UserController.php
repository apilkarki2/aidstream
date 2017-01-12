<?php namespace App\Http\Controllers\Lite\Users;

use App\Http\Controllers\Lite\LiteController;
use App\Lite\Services\FormCreator\Users;
use App\Lite\Services\Users\UserService;
use App\Lite\Services\Validation\ValidationService;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

/**
 * Class UserController
 * @package App\Http\Controllers\Lite\Users
 */
class UserController extends LiteController
{
    /**
     * @var Users
     */
    protected $userForm;
    /**
     * @var ValidationService
     */
    protected $validation;

    /**
     *
     */
    const ENTITY = 'Users';
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * UserController constructor.
     * @param UserService       $userService
     * @param Users             $userForm
     * @param ValidationService $validationService
     */
    public function __construct(UserService $userService, Users $userForm, ValidationService $validationService)
    {

        $this->userForm    = $userForm;
        $this->validation  = $validationService;
        $this->userService = $userService;
    }

    /**
     * Returns the view that displays list of users present in the organisation.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $users = $this->userService->all(session('org_id'));
        $roles = $this->userService->assignableRoles();

        return view('lite.users.index', compact('users', 'roles'));
    }

    /**
     * Return form to create the user.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $form                   = $this->userForm->form();
        $organizationIdentifier = auth()->user()->organization->user_identifier;

        return view('lite.users.create', compact('form', 'organizationIdentifier'));
    }

    /**
     * Stores user details in the database.
     *
     * @param Request $request
     * @return $this
     */
    public function store(Request $request)
    {
        if (!$this->validation->passes($request->all(), self::ENTITY, session('version'))) {
            return redirect()->back()->with('errors', $this->validation->errors())->withInput($request->all());
        }

        if ($this->userService->save($request->except(['_token', 'password_confirmation']))) {
            return redirect()->route('lite.users.index')->withResponse(['type' => 'success', 'code' => ['created', ['name' => trans('lite/global.user')]]]);
        }

        return redirect()->back()->withResponse(['type' => 'danger', 'code' => ['save_failed', ['name' => trans('lite/global.user')]]]);
    }

    /**
     * Delete the user.
     *
     * @param $userId
     */
    public function destroy($userId)
    {
        if ($this->userService->delete($userId)) {
            return redirect()->back()->withResponse(['type' => 'success', 'code' => ['deleted', ['name' => trans('lite/global.user')]]]);
        }

        return redirect()->back()->withResponse(['type' => 'danger', 'code' => ['delete_failed', ['name' => trans('lite/global.user')]]]);
    }

    /**
     * Updates the role of the user from AJAX Request.
     *
     * @param         $id
     * @param Request $request
     * @return string
     */
    public function updateRole($id, Request $request)
    {
        $roleId         = $request->get('role');
        $availableRoles = $this->userService->assignableRoleIds();

        if (in_array($roleId, $availableRoles)) {
            $this->userService->updateRole($id, $roleId);

            return 'success';
        }

        return 'failed';
    }
}

