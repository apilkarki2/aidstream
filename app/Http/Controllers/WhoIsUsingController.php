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
     *
     * @param ActivityManager      $activityManager
     * @param User                 $user
     * @param PerfectViewerManager $perfectViewerManager
     */
    function __construct(ActivityManager $activityManager, User $user, PerfectViewerManager $perfectViewerManager)
    {
        $this->activityManager      = $activityManager;
        $this->user                 = $user;
        $this->perfectViewerManager = $perfectViewerManager;
    }

    /**
     * Returns Organization count
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $organizations = $this->organizationQueryBuilder()->get();

        return view('who-is-using', compact('organizations'));
    }

    /**
     * Returns query of organizations published on Aidstream.
     *
     * @return mixed
     */
    public function organizationQueryBuilder()
    {
        return $this->perfectViewerManager->organizationQueryBuilder();
    }

    /**
     * Returns Activity Snapshot Query Builder
     *
     * @return ActivitySnapshot
     */
    protected function activityQueryBuilder()
    {
        return $this->perfectViewerManager->activityQueryBuilder();
    }

    /**
     * Returns required organizations for pagination to AJAX call.
     *
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

    /**
     * Returns data for Perfect Activity Viewer
     *
     * @param $orgId
     * @param $activityId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
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
     * Returns data for Perfect Organization Viewer
     *
     * @param $organizationId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDataForOrganization($organizationId)
    {
        $organizationIdExists = $this->organizationQueryBuilder()->where('org_slug', $organizationId)->get();

        if (count($organizationIdExists) == 0) {
            throw new NotFoundHttpException();
        }

        $activitySnapshot   = $this->perfectViewerManager->getSnapshotWithOrgId($organizationIdExists[0]->org_id);
        $organizations      = json_decode($organizationIdExists[0], true);
        $activities         = json_decode($activitySnapshot, true);
        $recipientCountries = $this->getRecipientCountries($activities);
        $user               = $this->user->getDataByOrgIdAndRoleId($organizationIdExists[0]->org_id, '1');

        return view('perfectViewer.organization-viewer', compact('activities', 'organizations', 'user', 'recipientCountries'));
    }

    /**
     * Provides Recipient Countries
     *
     * @param $activities
     * @return array
     */
    protected function getRecipientCountries($activities)
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

    /**
     * Provides a Description of an activity
     *
     * @param $description
     * @return string
     */
    protected function getDescription($description)
    {
        if (is_array($description)) {
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

    /**
     * Filters Description
     *
     * @param $activities
     * @return mixed
     */
    protected function filterDescription($activities)
    {
        foreach ($activities as $index => $value) {
            $activities[$index]['published_data']['description'] = $this->getDescription($activities[$index]['published_data']['description']);
        }

        return $activities;
    }

}
