<?php namespace App\Lite\Services\Validation\Rules\V202;

use App\Core\V201\Traits\GetCodes;

/**
 * Class Budget
 * @package App\Lite\Services\Validation\Rules\V202
 */
class Budget
{
    use GetCodes;

    /**
     * @var array
     */
    protected $settingsRules = [];

    /**
     * @var array
     */
    protected $settingsMessages = [];

    /**
     * @var array
     */
    protected $methods = [
        'StartDate',
        'EndDate',
        'Amount',
        'Currency'
    ];

    /**
     * @var string
     */
    protected $budget;

    /**
     * Budget constructor.
     */
    public function __construct()
    {
        $this->budget = getVal(request()->all(), ['budget'], []);
    }

    /**
     * @return array
     */
    public function rules()
    {
        foreach ($this->methods() as $method) {
            $methodName = sprintf('rulesFor%s', $method);

            if (method_exists($this, $methodName)) {
                $this->{$methodName}();
            }
        }

        return $this->settingsRules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        foreach ($this->methods() as $method) {
            $methodName = sprintf('messagesFor%s', $method);

            if (method_exists($this, $methodName)) {
                $this->{$methodName}();
            }
        }

        return $this->settingsMessages;
    }

    /**
     * @return array
     */
    protected function methods()
    {
        return $this->methods;
    }

    /**
     * Returns Start Date Rules of Budget
     *
     * @return $this
     */
    protected function rulesForStartDate()
    {
        foreach ($this->budget as $key => $val) {
            $this->settingsRules['budget.' . $key . '.startDate'] = 'required|date';
        }

        return $this;
    }

    /**
     * Returns Start Date Messages of Budget
     *
     * @return $this
     */
    protected function messagesForStartDate()
    {
        foreach ($this->budget as $key => $val) {
            $this->settingsMessages['budget.' . $key . '.startDate.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.period_start')]);
            $this->settingsMessages['budget.' . $key . '.startDate.date']     = trans('validation.date', ['attribute' => trans('lite/elementForm.period_start')]);
        }

        return $this;
    }

    /**
     * Returns End Date Rules of Budget
     *
     * @return $this
     */
    protected function rulesForEndDate()
    {
        foreach ($this->budget as $key => $val) {
            $this->settingsRules['budget.' . $key . '.endDate'] = sprintf('required|date|after:%s', 'budget.' . $key . '.startDate');
        }

        return $this;
    }

    /**
     * Returns End Date Messages of Budget
     *
     * @return $this
     */
    protected function messagesForEndDate()
    {
        foreach ($this->budget as $key => $val) {
            $this->settingsMessages['budget.' . $key . '.endDate.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.period_end')]);
            $this->settingsMessages['budget.' . $key . '.endDate.date']     = trans('validation.date', ['attribute' => trans('lite/elementForm.period_end')]);
            $this->settingsMessages['budget.' . $key . '.endDate.after']     = trans('validation.after', ['attribute' => trans('elementForm.period_end'), 'date' => trans('elementForm.period_start')]);
        }

        return $this;
    }

    /**
     * Returns Rules for Budget Amount
     *
     * @return $this
     */
    protected function rulesForAmount()
    {
        $this->settingsRules['budget.*.amount'] = 'required';

        return $this;
    }

    /**
     * Returns Messages for Budget Amount
     *
     * @return $this
     */
    protected function messagesForAmount()
    {
        $this->settingsMessages['budget.*.amount.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.amount')]);

        return $this;
    }

    /**
     * Returns Currency Rules for Budget
     *
     * @return $this
     */
    protected function rulesForCurrency()
    {
        $this->settingsRules['budget.*.currency'] = 'required';

        return $this;
    }

    /**
     * Returns Currency Messages for Budget
     * @return $this
     */
    protected function messagesForCurrency()
    {
        $this->settingsMessages['budget.*.currency.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.currency')]);

        return $this;
    }

}
