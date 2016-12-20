<?php namespace App\Core\V202\Forms\Settings;

use App\Core\Form\BaseForm;

/**
 * Class Classifications
 * @package App\Core\V202\Forms\Settings
 */
class Classifications extends BaseForm
{
    /**
     * build classifications form
     */
    public function buildForm()
    {
        $this
            ->addCheckBox('sector', trans('elementForm.sector'), true, 'readonly')
            ->addCheckBox('policy_marker', trans('elementForm.policy_marker'))
            ->addCheckBox('collaboration_type', trans('elementForm.collaboration_type'))
            ->addCheckBox('default_flow_type', trans('elementForm.default_flow_type'))
            ->addCheckBox('default_finance_type', trans('elementForm.default_finance_type'))
            ->addCheckBox('default_aid_type', trans('elementForm.default_aid_type'))
            ->addCheckBox('default_tied_status', trans('elementForm.default_tied_status'))
            ->addCheckBox('country_budget_items', trans('elementForm.country_budget_items'))
            ->addCheckBox('humanitarian_scope', trans('elementForm.humanitarian_scope'));
    }
}
