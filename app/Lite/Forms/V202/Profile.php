<?php namespace App\Lite\Forms\V202;

use App\Core\Form\BaseForm;

/**
 * Class Profile
 * @package App\Lite\Forms\V202
 */
class Profile extends BaseForm
{

    /**
     * Profile Form
     */
    public function buildForm()
    {
        return $this
            ->add('organisationName', 'text', ['label' => trans('lite/settings.organisation_name'), 'wrapper' => ['class' => 'form-group col-sm-6']])
            ->add('organisationAddress', 'text', ['label' => trans('lite/profile.organisation_address'), 'wrapper' => ['class' => 'form-group col-sm-6']])
            ->addSelect(
                'organisationCountry',
                $this->getCodeList('Country', 'Organization'),
                trans('lite/profile.organisation_country'),
                null,
                config('app.default_language'),
                false,
                [
                    'wrapper' => ['class' => 'form-group col-sm-6']
                ]
            )
            ->add('organisationUrl', 'text', ['label' => trans('lite/profile.organisation_url'), 'wrapper' => ['class' => 'form-group col-sm-6']])
            ->add('organisationTelephone', 'text', ['label' => trans('lite/profile.organisation_telephone'), 'wrapper' => ['class' => 'form-group col-sm-6']])
            ->add('organisationTwitter', 'text', ['label' => trans('lite/profile.organisation_twitter'), 'wrapper' => ['class' => 'form-group col-sm-6']])
            ->add(trans('lite/settings.save'), 'submit', ['attr' => ['class' => 'btn btn-submit btn-form'], 'wrapper' => ['class' => 'form-group col-sm-6']]);
    }
}
