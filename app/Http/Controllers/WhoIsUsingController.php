<?php namespace App\Http\Controllers;

use App\Models\PerfectViewer\ActivitySnapshot;
use App\Services\Activity\ActivityManager;
use App\Services\Organization\OrganizationManager;
use App\Services\PerfectViewer\PerfectViewerManager;
use App\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class WhoIsUsingController
 * @package App\Http\Controllers
 */
class WhoIsUsingController extends Controller
{

    /**
     * @var ActivitySnapshot
     */
    protected $perfectViewerManager;

    /**
     * WhoIsUsingController constructor.
     * @param ActivityManager      $activityManager
     * @param OrganizationManager  $organizationManager
     * @param User                 $user
     * @param PerfectViewerManager $perfectViewerManager
     */
    function __construct(ActivityManager $activityManager, OrganizationManager $organizationManager, User $user, PerfectViewerManager $perfectViewerManager)
    {
        $this->activityManager      = $activityManager;
        $this->orgManager           = $organizationManager;
        $this->user                 = $user;
        $this->perfectViewerManager = $perfectViewerManager;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $organizationCount = $this->organizationQueryBuilder()->get()->count();

        return view('who-is-using', compact('organizationCount'));
    }

    /** Returns query of organizations published on Aidstream.
     * @return mixed
     */
    public function organizationQueryBuilder()
    {
        return $this->perfectViewerManager->organizationQueryBuilder();
    }

    protected function activityQueryBuilder()
    {
        return $this->perfectViewerManager->activityQueryBuilder();
    }

    /**
     * return organization list
     * @param int $page
     * @param int $count
     * @return mixed
     */
    public function listOrganization($page = 0, $count = 20)
    {
        $skip                  = $page * $count;
        $data['next_page']     = $this->organizationQueryBuilder()->get()->count() > ($skip + $count);
        $data['organizations'] = $this->organizationQueryBuilder()->skip($skip)->take($count)->get();

        return $data;
    }


    public function showActivity($orgId, $activityId)
    {
        $organizationIdExists = $this->organizationQueryBuilder()->where('org_slug', $orgId)->get();
        if (count($organizationIdExists) == 0) {
            throw new NotFoundHttpException();
        }
        $activityIdExists = $this->activityQueryBuilder()->where('activity_id', $activityId)->get();
        if (count($activityIdExists) == 0) {
            throw new NotFoundHttpException();
        }
        $recipientCountries = $this->getRecipientCountries($activityIdExists);

        $user = $this->user->getDataByOrgIdAndRoleId($organizationIdExists[0]->org_id, '1');

        $organization = json_decode($organizationIdExists, true);
        $activity     = json_decode($activityIdExists, true);

        $activity = $this->filterDescription($activity);

        return view('perfectViewer.activity-viewer', compact('organization', 'activity', 'user', 'recipientCountries'));
    }

    /**
     * @param $organizationId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDataForOrganization($organizationId)
    {
        $organizationIdExists = $this->organizationQueryBuilder()->where('org_slug', $organizationId)->get();

        if (count($organizationIdExists) == 0) {
            throw new NotFoundHttpException();
        }

        $activitySnapshot = $this->perfectViewerManager->getSnapshotWithOrgId($organizationIdExists[0]->org_id);
        $organizations = json_decode($organizationIdExists[0], true);
        $activities    = json_decode($activitySnapshot, true);
        $recipientCountries = $this->getRecipientCountries($activities);
        $user = $this->user->getDataByOrgIdAndRoleId($organizationIdExists[0]->org_id, '1');

        return view('perfectViewer.organization-viewer', compact('activities', 'organizations', 'user', 'recipientCountries'));
    }

    private function getRecipientCountries($activities)
    {
        $recipientCountries = [];
        foreach ($activities as $index => $activity) {
            foreach ($activity['published_data']['transactions'] as $tranIndex => $transaction) {
                $recipientCountries[] = getVal($transaction, ['transaction', 'recipient_country', 0, 'country_code'], '');
            }
        }

        $recipientCountries = array_unique($recipientCountries);

        return $recipientCountries;
    }

    private function getDescription($description)
    {
        if(is_array($description)) {
            foreach ($description as $index => $value) {
                if ($value['type'] == 1) {
                    return getVal($value, ['narrative', 0, 'narrative'], '');
                }
                if ($value['type'] == 2) {
                    return getVal($value, ['narrative', 0, 'narrative'], '');
                }
                if ($value['type'] == 3) {
                    return getVal($value, ['narrative', 0, 'narrative'], '');
                }
                if ($value['type'] == 4) {
                    return getVal($value, ['narrative', 0, 'narrative'], '');
                }
            }
        }
        return '';
    }

    private function filterDescription($activities)
    {
        foreach($activities as $index => $value){
            $activities[$index]['published_data']['description'] = $this->getDescription($activities[$index]['published_data']['description']);
        }
        return $activities;
    }

}
