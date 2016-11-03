<?php namespace App\Http\Controllers\Complete\Xml;

use App\Http\Controllers\Controller;
use App\Http\Requests\Xml\XmlUploadRequest;
use App\Services\XmlImporter\XmlImportManager\XmlImportManager;

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
        $file    = $request->file('xml_file');

        if (!$this->xmlImportManager->import($file)) {
            return redirect()->back()->withResponse(['type' => 'warning', 'code' => ['message', ['message' => 'Xml could not be imported. Please try again later.']]]);
        }

        return redirect()->back()->withResponse(['type' => 'success', 'code' => ['message', ['message' => 'Xml successfully be imported.']]]);
    }
}
