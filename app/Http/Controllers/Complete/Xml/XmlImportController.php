<?php namespace App\Http\Controllers\Complete\Xml;

use App\Http\Controllers\Controller;
use App\Http\Requests\Xml\XmlUploadRequest;
use App\Services\XmlImporter\XmlImportManager;
use Illuminate\Support\Facades\Session;

/**
 * Class XmlImportController
 * @package App\Http\Controllers\Complete\Xml
 */
class XmlImportController extends Controller
{
    /**
     * @var XmlImportManager
     */
    protected $xmlImportManager;

    /**
     * XmlImportController constructor.
     * @param XmlImportManager $xmlImportManager
     */
    public function __construct(XmlImportManager $xmlImportManager)
    {
        $this->middleware('auth');
        $this->xmlImportManager = $xmlImportManager;
    }

    /**
     * Show the form to upload xml file.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('xmlImport.index');
    }

    /**
     * Store the Xml file and start import process.
     *
     * @param XmlUploadRequest $request
     * @return mixed
     */
    public function store(XmlUploadRequest $request)
    {
        $file = $request->file('xml_file');
        if ($this->xmlImportManager->store($file)) {
            $userId = auth()->user()->id;
            $this->xmlImportManager->startImport($file->getClientOriginalName(), $userId, session('org_id'));

//            return redirect()->route('xml-import.status');
        }

//        if (($result = $this->xmlImportManager->import($request->file('xml_file')))) {
//            return redirect()->back()->withResponse(['type' => 'success', 'code' => ['message', ['message' => 'Xml successfully be imported.']]]);
//        }
//
//        if (false == $result) {
//            return redirect()->back()->withResponse(['type' => 'warning', 'code' => ['message', ['message' => 'The uploaded Xml file contains malformed Xml contents.']]]);
//        }
//
//        return redirect()->back()->withResponse(['type' => 'warning', 'code' => ['message', ['message' => 'Xml could not be imported. Please try again later.']]]);

//        $response = ['type' => 'success', 'code' => ['message', ['message' => 'Your activity is being imported. Please wait']]];
        session(['xml_import_status' => 'started']);

        return redirect()->route('activity.index');
    }

    /**
     * Check the Xml Import status.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function status()
    {
        $completedActivity    = $this->xmlImportManager->loadJsonFile('xml_completed_status.json');
        $totalActivities      = 0;
        $currentActivityCount = 0;
        $failed               = 0;
        $success              = 0;

        if ($completedActivity) {
            $totalActivities      = getVal($completedActivity, ['total_activities']);
            $currentActivityCount = getVal($completedActivity, ['current_activity_count']);
            $failed               = getVal($completedActivity, ['failed']);
            $success              = getVal($completedActivity, ['success']);
        }

        return response()->json(['totalActivities' => $totalActivities, 'currentActivityCount' => $currentActivityCount, 'failed' => $failed, 'success' => $success]);
    }

    /**
     * Check if the import process is complete.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function isCompleted()
    {
        $completedActivity = $this->xmlImportManager->loadJsonFile('xml_completed_status.json');
        $status            = 'incomplete';
        if ($completedActivity) {
            $totalActivities      = getVal($completedActivity, ['total_activities']);
            $currentActivityCount = getVal($completedActivity, ['current_activity_count']);
            if ($currentActivityCount === $totalActivities) {
                $status = 'completed';
            }
        }

        return response()->json(['status' => $status]);
    }

    public function complete()
    {
        Session::forget('xml_import_status');
        $this->xmlImportManager->removeTemporaryXmlFolder();
    }
}
