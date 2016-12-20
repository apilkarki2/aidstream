<?php namespace App\Core\V201\Forms\Activity;

use App\Core\Form\BaseForm;

/**
 * Class OwnerOrg
 * Contains the function that creates Owner Org Form
 * @package App\Core\V201\Forms\Activity
 */
class OwnerOrg extends BaseForm
{
    public function buildForm()
    {
        $this
            ->add('reference', 'text', ['label' => trans('elementForm.reference'), 'help_block' => $this->addHelpText('Activity_OtherActivityIdentifier_OwnerOrg-ref')])
            ->addNarrative('owner_organization_narrative', trans('elementForm.owner_organisation_narrative'))
            ->addAddMoreButton('add_owner_organization_narrative', 'owner_organization_narrative');
    }
}
