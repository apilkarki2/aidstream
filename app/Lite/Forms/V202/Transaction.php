<?php namespace App\Lite\Forms\V202;


use App\Lite\Forms\LiteBaseForm;

/**
 * Class TransactionForm
 * @package App\Lite\Forms\V202
 */
class Transaction extends LiteBaseForm
{
    /**
     * Form structure for funding organisation and implementing organisation.
     */
    public function buildForm()
    {
        $currency = $this->getCodeList('Currency', 'Activity');

        $type = explode("[", $this->name);

        $organisation = trans('lite/elementForm.receiver_organisation');

        if($type[0] == 'incomingfunds')
        {
            $organisation = trans('lite/elementForm.provider_organisation');
        }

        $this->add('reference', 'text', ['label' => trans('lite/elementForm.reference'), 'required' => true, 'wrapper' => ['class' => 'form-group col-sm-6']])
             ->add('date', 'date', ['label' => trans('lite/elementForm.date'), 'required' => true, 'wrapper' => ['class' => 'form-group col-sm-6']])
             ->add('amount', 'text', ['label' => trans('lite/elementForm.amount'), 'required' => true, 'wrapper' => ['class' => 'form-group col-sm-6']])
             ->addSelect(
                 'currency',
                 $currency,
                 trans('lite/elementForm.currency'),
                 null,
                 null,
                 true,
                 [
                     'wrapper' => ['class' => 'form-group col-sm-6']
                 ]
             )
             ->add('description', 'text', ['label' => trans('lite/elementForm.description'), 'wrapper' => ['class' => 'form-group col-sm-6']])
             ->add('organisation', 'text', ['label' => $organisation, 'wrapper' => ['class' => 'form-group col-sm-6']])
             ->add(
                 'remove_button',
                 'button',
                 [
                     'required' => true,
                     'label'    => 'Remove This',
                     'attr'     => [
                         'class' => 'remove_from_collection',
                     ],
                 ]
             );
    }
}
