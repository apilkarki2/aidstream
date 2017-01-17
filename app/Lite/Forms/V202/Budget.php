<?php namespace App\Lite\Forms\V202;


use App\Lite\Forms\LiteBaseForm;

/**
 * Class BudgetForm
 * @package App\Lite\Forms\V202
 */
class Budget extends LiteBaseForm
{
    /**
     * Form structure for funding organisation and implementing organisation.
     */
    public function buildForm()
    {
        $required = true;

        $currency = $this->getCodeList('Currency', 'Activity');

        $this->add('startDate', 'date', ['label' => trans('lite/elementForm.period_start'), 'required' => $required, 'wrapper' => ['class' => 'form-group col-sm-6']])
             ->add('endDate', 'date', ['label' => trans('lite/elementForm.period_end'), 'required' => $required, 'wrapper' => ['class' => 'form-group col-sm-6']])
             ->add('amount', 'text', ['label' => trans('lite/elementForm.amount'), 'required' => $required, 'wrapper' => ['class' => 'form-group col-sm-6']])
             ->addSelect(
                 'currency',
                 $currency,
                 trans('lite/elementForm.currency'),
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
                     'required' => $required,
                     'label'    => 'Remove This',
                     'attr'     => [
                         'class' => 'remove_from_collection',
                     ],
                 ]
             );
    }
}
