<?php namespace App\Lite\Forms\V202;

use App\Core\Form\BaseForm;

class Settings extends BaseForm
{
    public function __construct()
    {

    }

    public function buildForm()
    {
        $this
            ->add('organisationName', 'text', ['label' => trans('lite/settings.organisation_name'), 'required' => true, 'wrapper' => ['class' => 'form-group col-sm-6']])
            ->addSelect(
                'language',
                $this->getCodeList('Language', 'Organization'),
                'Language',
                $this->addHelpText('activity_defaults-default_language', false),
                config('app.default_language'),
                true,
                [
                    'wrapper' => ['class' => 'form-group col-sm-6']
                ]
            )
            ->add('organisationIdentifier', 'text', ['label' => trans('lite/settings.organisation_identifier'), 'required' => true, 'wrapper' => ['class' => 'form-group col-sm-6']])
            ->addSelect(
                'organisationType',
                $this->getCodeList('OrganizationType', 'Organization'),
                'Organisation Type',
                null,
                config('app.default_language'),
                true,
                [
                    'wrapper' => ['class' => 'form-group col-sm-6']
                ]
            )
            ->add('publisherId', 'text', ['label' => trans('lite/settings.publisher_id'), 'wrapper' => ['class' => 'form-group col-sm-6']])
            ->add('apiKey', 'text', ['label' => trans('lite/settings.api_key'), 'wrapper' => ['class' => 'form-group col-sm-6']])
            ->add(
                'autoUpdateIatiRegistryWhenPublished',
                'choice',
                [
                    'label'   => 'Automatically Update to the IATI Registry when publishing files?',
                    'choices' => ['yes' => 'Yes', 'no' => 'No'],
                    'expanded'       => true,
                    'default_value'  => 'no',
                    'choice_options' => [
                        'wrapper' => ['class' => 'choice-wrapper form-group col-sm-6']
                    ],
                    'checked' => false
                ]
            )
            ->addSelect(
                'defaultCurrency',
                $this->getCodeList('Currency', 'Organization'),
                'Default Currency',
                $this->addHelpText('activity_defaults-default_language', false),
                config('app.default_language'),
                true,
                [
                    'wrapper' => ['class' => 'form-group col-sm-6']
                ]
            )
            ->addSelect(
                'defaultLanguage',
                $this->getCodeList('Language', 'Organization'),
                'Default Language',
                $this->addHelpText('activity_defaults-default_language', false),
                config('app.default_language'),
                true,
                [
                    'wrapper' => ['class' => 'form-group col-sm-6']
                ]
            )
            ->add('Save', 'submit', ['attr' => ['class' => 'btn btn-submit btn-form'], 'wrapper' => ['class' => 'form-group col-sm-6']]);

    }

}