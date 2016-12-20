<?php namespace App\Core\V201\Requests\Activity;

/**
 * Class Condition
 * @package App\Core\V201\Requests\Activity
 */
class Condition extends ActivityBaseRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return $this->getRulesForCondition($this->get('condition'));
    }

    /**
     * @return array
     */
    public function messages()
    {
        return $this->getMessagesForCondition($this->get('condition'));
    }

    /**
     * @param array $formFields
     * @return array
     */
    protected function getRulesForCondition(array $formFields)
    {
        $rules = [];

        foreach ($formFields as $conditionIndex => $condition) {
            $conditionForm                                       = sprintf('condition.%s', $conditionIndex);
            $rules['condition_attached']                         = 'required';
            $rules[sprintf('%s.condition_type', $conditionForm)] = 'required_if:condition_attached,1';
            $rules                                               = array_merge(
                $rules,
                $this->getRulesForNarrative($condition['narrative'], $conditionForm)
            );

            foreach ($condition['narrative'] as $narrativeIndex => $narrative) {
                $rules[sprintf('%s.narrative.%s.narrative', $conditionForm, $narrativeIndex)][] = 'required_if:condition_attached,1';
            }
        }

        return $rules;
    }

    /**
     * @param array $formFields
     * @return array
     */
    protected function getMessagesForCondition(array $formFields)
    {
        $messages = [];

        foreach ($formFields as $conditionIndex => $condition) {
            $conditionForm                                                      = sprintf('condition.%s', $conditionIndex);
            $messages['condition_attached.required']                            = trans('validation.required', ['attribute' => trans('elementForm.condition_attached')]);
            $messages[sprintf('%s.condition_type.required_if', $conditionForm)] = trans(
                'validation.required_if',
                ['attribute' => trans('elementForm.condition_type'), 'values' => trans('elementForm.condition_attached'), 'value' => trans('elementForm.yes')]
            );
            $messages                                                           = array_merge(
                $messages,
                $this->getMessagesForNarrative($condition['narrative'], $conditionForm)
            );

            foreach ($condition['narrative'] as $narrativeIndex => $narrative) {
                $messages[sprintf('%s.narrative.%s.narrative.required_if', $conditionForm, $narrativeIndex)] = trans(
                    'validation.required_if',
                    ['attribute' => trans('elementForm.narrative'), 'values' => trans('elementForm.condition_attached'), 'value' => trans('elementForm.yes')]
                );
            }
        }

        return $messages;
    }
}
