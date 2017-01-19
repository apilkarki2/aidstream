<?php namespace App\Lite\Services\Validation\Rules\V202;

use App\Core\V201\Traits\GetCodes;

/**
 * Class Transaction
 * @package App\Lite\Services\Validation\Rules\V202
 */
class Transaction
{
    use GetCodes;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'transaction.*.reference' => 'required',
            'transaction.*.date'      => 'required|date',
            'transaction.*.amount'    => 'required',
            'transaction.*.currency'  => 'required'
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'transaction.*.reference.required' => trans('validation.required', ['attribute' => trans('lite/elementForm.reference')]),
            'transaction.*.date.required'      => trans('validation.required', ['attribute' => trans('lite/elementForm.date')]),
            'transaction.*.date.date'          => trans('validation.date', ['attribute' => trans('lite/elementForm.date')]),
            'transaction.*.amount.required'    => trans('validation.required', ['attribute' => trans('lite/elementForm.amount')]),
            'transaction.*.currency.required'  => trans('validation.required', ['attribute' => trans('lite/elementForm.currency')]),
        ];
    }
}
