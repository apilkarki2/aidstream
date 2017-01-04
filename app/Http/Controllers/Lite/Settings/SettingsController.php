<?php namespace App\Http\Controllers\Lite\Settings;

use App\Http\Controllers\Lite\LiteController;
use Kris\LaravelFormBuilder\FormBuilder;

class SettingsController extends LiteController
{
    /**
     * @var FormBuilder
     */
    protected $formBuilder;

    public function __construct(FormBuilder $formBuilder)
    {
        $this->middleware('auth');
        $this->formBuilder = $formBuilder;
    }

    public function create()
    {
        $form = $this->formBuilder->create('App\Lite\Forms\V202\Settings');

        return view('lite.settings.index', compact('form'));
    }

    public function edit()
    {
        $form = $this->formBuilder->create('App\Lite\Forms\V202\Settings');

        return view('lite.settings.index', compact('form'));
    }

}