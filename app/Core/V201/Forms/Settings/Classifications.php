<?php namespace App\Core\V201\Forms\Settings;

use App\Core\Form\BaseForm;

/**
 * Class Classifications
 * @package App\Core\V201\Forms\Settings
 */
class Classifications extends BaseForm
{
    /**
     * build classifications form
     */
    public function buildForm()
    {
        $this
            ->addCheckBox('sector', 'Sector', true, 'readonly')
            ->addCheckBox('policy_marker', trans('elementForm.policy_marker'))
            ->addCheckBox('collaboration_type', trans('elementForm.collaboration_type'))
            ->addCheckBox('default_flow_type', trans('elementForm.default_flow_type'))
            ->addCheckBox('default_finance_type', trans('elementForm.default_finance_type'))
            ->addCheckBox('default_aid_type', trans('elementForm.default_aid_type'))
            ->addCheckBox('default_tied_status', trans('elementForm.default_tied_status'))
            ->addCheckBox('country_budget_items', 'Country Budget Items');
    }
}
