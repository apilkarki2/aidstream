<?php namespace App\Http\Controllers\Complete\Organization;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\Organization\OrganizationManager;
use App\Services\Organization\TotalExpenditureManager;
use App\Services\RequestManager\Organization\TotalExpenditureRequestManager;
use Illuminate\Support\Facades\Gate;
use Session;
use URL;
use App\Http\Requests\Request;
use App\Services\FormCreator\Organization\TotalExpenditure as FormBuilder;

/**
 * Class TotalExpenditureController
 * @package App\Http\Controllers\Complete\Organization
 */
class TotalExpenditureController extends Controller
{

    /**
     * @var
     */
    protected $formBuilder;

    /**
     * @var OrganizationManager
     */
    protected $organizationManager;

    /**
     * @var FormBuilder
     */
    protected $totalExpenditureForm;

    /**
     * @var TotalExpenditureManager
     */
    protected $totalExpenditureManager;

    /**
     * @param FormBuilder             $formBuilder
     * @param TotalExpenditureManager $totalExpenditureManager
     * @param OrganizationManager     $organizationManager
     */
    public function __construct(
        FormBuilder $formBuilder,
        TotalExpenditureManager $totalExpenditureManager,
        OrganizationManager $organizationManager
    ) {
        $this->middleware('auth');
        $this->middleware('role');
        $this->totalExpenditureForm    = $formBuilder;
        $this->organizationManager     = $organizationManager;
        $this->totalExpenditureManager = $totalExpenditureManager;
    }

    /**
     * @param $orgId
     * @return \Illuminate\View\View
     */
    public function index($orgId)
    {
        $organization = $this->organizationManager->getOrganization($orgId);

        if (Gate::denies('belongsToOrganization', $organization)) {
            return redirect()->back()->withResponse($this->getNoPrivilegesMessage());
        }

        $totalExpenditure = $this->totalExpenditureManager->getOrganizationTotalExpenditureData($orgId);
        $form             = $this->totalExpenditureForm->editForm($totalExpenditure, $orgId);

        return view('Organization.totalExpenditure.edit', compact('form', 'totalExpenditure','orgId'));
    }

    /**
     *
     * @param                                $orgId
     * @param Request                        $request
     * @param TotalExpenditureRequestManager $expenditureRequestManager
     * @return mixed
     */
    public function update($orgId, Request $request, TotalExpenditureRequestManager $expenditureRequestManager)
    {
        $organization = $this->organizationManager->getOrganization($orgId);
        $organizationData = $this->totalExpenditureManager->getOrganizationData($orgId);

        if (Gate::denies('belongsToOrganization', $organization)) {
            return redirect()->back()->withResponse($this->getNoPrivilegesMessage());
        }

        $organizationData = $this->totalExpenditureManager->getOrganizationData($orgId);
        $this->authorizeByRequestType($organizationData, 'total_expenditure');
        $totalExpenditure = $request->all();

        if ($this->totalExpenditureManager->update($totalExpenditure, $organizationData)) {
            $this->organizationManager->resetStatus($orgId);
            $response = ['type' => 'success', 'code' => ['updated', ['name' => 'Organization Total Expenditure']]];

            return redirect()->to(sprintf('/organization/%s', $orgId))->withResponse($response);
        }
        $response = ['type' => 'danger', 'code' => ['update_failed', ['name' => 'Organization Total Expenditure']]];

        return redirect()->back()->withInput()->withResponse($response);
    }
}
