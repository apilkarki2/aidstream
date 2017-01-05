<?php namespace App\Lite\Forms\V202;


use App\Lite\Forms\LiteBaseForm;

/**
 * Class ParticipatingOrganisation
 * @package App\Lite\Forms\V202
 */
class ParticipatingOrganisation extends LiteBaseForm
{
    /**
     * Form structure for funding organisation and implementing organisation.
     */
    public function buildForm()
    {
        $required = true;

        if (substr($this->name, 0, 21) == 'funding_organisations') {
            $required = false;
        }

        $organisationTypes = $this->getCodeList('OrganisationType', 'Activity');

        $this->addText('organisation_name', trans('lite/elementForm.organisation_name'), $required)
             ->addSelect(
                 'organisation_type',
                 $organisationTypes,
                 trans('lite/elementForm.organisation_type'),
                 null,
                 null,
                 $required,
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