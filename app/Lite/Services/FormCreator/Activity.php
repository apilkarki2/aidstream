<?php namespace App\Lite\Services\FormCreator;


use App\Lite\Forms\FormPathProvider;
use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\FormBuilder;

class Activity
{
    use FormPathProvider;

    /**
     * @var FormBuilder
     */
    protected $formBuilder;

    /**
     * Activity constructor.
     * @param FormBuilder $formBuilder
     */
    public function __construct(FormBuilder $formBuilder)
    {
        $this->formBuilder = $formBuilder;
    }

    /**
     * @param null $model
     * @return Form
     */
    public function form($model = null)
    {
        $formPath = $this->getFormPath('Activity', 'V202');

        return $this->formBuilder->create(
            $formPath,
            [
                'method' => 'post',
                'model'  => $model,
                'url'    => route('lite.activity.store')
            ]
        )->add('Save', 'submit', ['attr' => ['class' => 'btn btn-submit btn-form']]);
    }
}