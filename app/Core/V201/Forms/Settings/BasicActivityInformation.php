<?php namespace App\Core\V201\Forms\Settings;

use App\Core\Form\BaseForm;

/**
 * Class BasicActivityInformation
 * @package App\Core\V201\Forms\Settings
 */
class BasicActivityInformation extends BaseForm
{
    /**
     * build basic activity information form
     */
    public function buildForm()
    {
        $this
            ->addCheckBox('title', 'Title', true, 'readonly')
            ->addCheckBox('description', 'Description', true, 'readonly')
            ->addCheckBox('activity_status', trans('elementForm.activity_status'), true, 'readonly')
            ->addCheckBox('activity_date', trans('elementForm.activity_date'), true, 'readonly')
            ->addCheckBox('contact_info', 'Contact Info')
            ->addCheckBox('activity_scope', trans('elementForm.activity_scope'));
    }
}
