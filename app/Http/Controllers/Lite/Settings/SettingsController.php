<?php namespace App\Http\Controllers\Lite\Settings;

use App\Http\Controllers\Lite\LiteController;
use App\Http\Requests\Request;
use App\Lite\Services\Settings\SettingsService;
use Kris\LaravelFormBuilder\FormBuilder;

class SettingsController extends LiteController
{
    /**
     * @var FormBuilder
     */
    protected $formBuilder;
    /**
     * @var SettingsService
     */
    private $settingsService;

    /**
     * SettingsController constructor.
     * @param FormBuilder     $formBuilder
     * @param SettingsService $settingsService
     */
    public function __construct(FormBuilder $formBuilder, SettingsService $settingsService)
    {
        $this->middleware('auth');
        $this->formBuilder     = $formBuilder;
        $this->settingsService = $settingsService;
    }

    public function create()
    {
        $form = $this->formBuilder->create(
            'App\Lite\Forms\V202\Settings',
            [
                'method' => 'PUT',
                'model'  => [],
                'url'    => route('lite.settings.store')
            ]
        );

        return view('lite.settings.index', compact('form'));
    }

    public function edit()
    {

        $model = $this->settingsService->getSettingsModel();

        $form = $this->formBuilder->create(
            'App\Lite\Forms\V202\Settings',
            [
                'method' => 'PUT',
                'model'  => $model,
                'url'    => route('lite.settings.store')
            ]
        );

        return view('lite.settings.index', compact('form'));
    }

    public function store(Request $request)
    {
        if ($this->settingsService->store($request->all())) {
            return redirect()->route('lite.settings.edit')->withResponse(['type' => 'success', 'messages' => ['Settings saved successfully.']]);
        }

        return redirect()->route('lite.settings.edit')->withResponse(['type' => 'danger', 'messages' => ['Error occured during saving.']]);
    }

}