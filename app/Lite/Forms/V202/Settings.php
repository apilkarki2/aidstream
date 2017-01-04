<?php namespace App\Lite\Forms\V202;

use App\Core\Form\BaseForm;

class Settings extends BaseForm
{

    public function buildForm()
    {
        $this
            ->add('organisationName', 'text', ['label' => trans('lite/settings.organisation_name'), 'required' => true, 'wrapper' => ['class' => 'form-group col-sm-6']])
            ->addSelect(
                'language',
                $this->getCodeList('Language', 'Organization'),
                trans('lite/settings.language'),
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
                trans('lite/settings.organisation_type'),
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
                'publishFiles',
                'choice',
                [
                    'label'         => trans('lite/settings.automatically_update_iati_text'),
                    'choices'       => ['yes' => trans('lite/settings.yes'), 'no' => trans('lite/settings.no')],
                    'expanded'      => true,
                    'default_value' => 'no',
                    'wrapper'       => ['class' => 'form-group col-sm-6'],
                    'checked'       => false
                ]
            )
            ->addSelect(
                'defaultCurrency',
                $this->getCodeList('Currency', 'Organization'),
                trans('lite/settings.default_currency'),
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
                trans('lite/settings.default_language'),
                $this->addHelpText('activity_defaults-default_language', false),
                config('app.default_language'),
                true,
                [
                    'wrapper' => ['class' => 'form-group col-sm-6']
                ]
            )
            ->add(trans('lite/settings.save'), 'submit', ['attr' => ['class' => 'btn btn-submit btn-form'], 'wrapper' => ['class' => 'form-group col-sm-6']]);
    }

}