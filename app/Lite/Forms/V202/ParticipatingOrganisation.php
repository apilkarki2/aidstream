<?php namespace App\Lite\Forms\V202;


use App\Lite\Forms\LiteBaseForm;

class ParticipatingOrganisation extends LiteBaseForm
{
    public function buildForm()
    {
        $organisationTypes = $this->getCodeList('OrganisationType', 'Activity');

        $this->addText('organisation_name', trans('lite/elementForm.organisation_name'))
             ->addSelect(
                 'organisation_type',
                 $organisationTypes,
                 trans('lite/elementForm.organisation_type'),
                 null,
                 null,
                 true,
                 [
                     'wrapper' => ['class' => 'form-group col-sm-6']
                 ]
             )
             ->add(
                 'remove_button',
                 'button',
                 [
                     'label' => 'Remove This',
                     'attr'  => [
                         'class' => 'remove_from_collection',
                     ],
                 ]
             );
    }
}