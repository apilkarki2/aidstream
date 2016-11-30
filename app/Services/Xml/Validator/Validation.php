<?php namespace App\Services\Xml\Validator;


use App\Services\Xml\Validator\Traits\RegistersValidationRules;
use Illuminate\Validation\Factory;
use Symfony\Component\Translation\TranslatorInterface;

//use Illuminate\Contracts\Validation\Factory;


class Validation extends Factory
{
    use RegistersValidationRules;

    protected $validator;

    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct($translator);

        $this->registerValidationRules();
    }

    public function initialize($activity, $rules, $messages)
    {
        $this->validator = $this->make($activity, $rules, $messages);

        return $this;
    }

    public function passes()
    {
        $this->validator->passes();

        return $this;
    }

    public function withErrors($activityId)
    {
        $errors = [];
        foreach ($this->errors() as $index => $error) {
            $element            = $this->parseErrors($index);
            $errors[$element][] = getVal($error, [0], '');
        }

        return $errors;
    }

    protected function parseErrors($index)
    {
        $element = getVal(explode('.', $index), [0], '');

        return ucwords(((str_replace('_', ' ', $element))));
    }

    protected function errors()
    {
        return $this->validator->errors()->getMessages();
    }

    /**
     * returns rules for narrative
     * @param $elementNarrative
     * @param $elementName
     * @return array
     */
    public function getRulesForNarrative($elementNarrative, $elementName)
    {
        $rules                                          = [];
        $rules[sprintf('%s.narrative', $elementName)][] = 'unique_lang';
        $rules[sprintf('%s.narrative', $elementName)][] = 'unique_default_lang';

        foreach ($elementNarrative as $narrativeIndex => $narrative) {
            $rules[sprintf('%s.narrative.%s.narrative', $elementName, $narrativeIndex)][] = 'required_with_language';
        }

        return $rules;
    }

    /**
     * returns messages for narrative
     * @param $elementNarrative
     * @param $elementName
     * @return array
     */
    public function getMessagesForNarrative($elementNarrative, $elementName)
    {
        $messages                                                    = [];
        $messages[sprintf('%s.narrative.unique_lang', $elementName)] = 'Languages should be unique.';

        foreach ($elementNarrative as $narrativeIndex => $narrative) {
            $messages[sprintf('%s.narrative.%s.narrative.required_with_language', $elementName, $narrativeIndex)] = 'Narrative is required with language.';
        }

        return $messages;
    }

    /**
     * returns rules for narrative if narrative is required
     * @param $elementNarrative
     * @param $elementName
     * @return array
     */
    public function getRulesForRequiredNarrative($elementNarrative, $elementName)
    {
        $rules                                          = [];
        $rules[sprintf('%s.narrative', $elementName)][] = 'unique_lang';
        $rules[sprintf('%s.narrative', $elementName)][] = 'unique_default_lang';

        foreach ($elementNarrative as $narrativeIndex => $narrative) {
            if (boolval($narrative['language'])) {
                $rules[sprintf('%s.narrative.%s.narrative', $elementName, $narrativeIndex)] = 'required_with:' . sprintf(
                        '%s.narrative.%s.language',
                        $elementName,
                        $narrativeIndex
                    );
            } else {
                $rules[sprintf('%s.narrative.%s.narrative', $elementName, $narrativeIndex)] = 'required';
            }
        }

        return $rules;
    }

    /**
     * get message for narrative
     * @param $elementNarrative
     * @param $elementName
     * @return array
     */
    public function getMessagesForRequiredNarrative($elementNarrative, $elementName)
    {
        $messages                                                    = [];
        $messages[sprintf('%s.narrative.unique_lang', $elementName)] = 'Languages should be unique';

        foreach ($elementNarrative as $narrativeIndex => $narrative) {
            if (boolval($narrative['language'])) {
                $messages[sprintf(
                    '%s.narrative.%s.narrative.required_with',
                    $elementName,
                    $narrativeIndex
                )] = 'Narrative is required with language';
            } else {
                $messages[sprintf(
                    '%s.narrative.%s.narrative.required',
                    $elementName,
                    $narrativeIndex
                )] = 'Narrative is required';
            }
        }

        return $messages;
    }

    /**
     * get rules for transaction's sector element
     * @param $sector
     * @param $formFields
     * @param $formBase
     * @return array
     */
    public function getRulesForTransactionSectorNarrative($sector, $formFields, $formBase)
    {
        $rules                                       = [];
        $rules[sprintf('%s.narrative', $formBase)][] = 'unique_lang';
        $rules[sprintf('%s.narrative', $formBase)][] = 'unique_default_lang';
        foreach ($formFields as $narrativeIndex => $narrative) {
            $rules[sprintf('%s.narrative.%s.narrative', $formBase, $narrativeIndex)][] = 'required_with_language';
            if ($narrative['narrative'] != "") {
                $rules[sprintf('%s.sector_vocabulary', $formBase)] = 'required_with:' . sprintf('%s.narrative.%s.narrative', $formBase, $narrativeIndex);
                if ($sector['sector_vocabulary'] == 1 || $sector['sector_vocabulary'] == 2) {
                    if ($sector['sector_vocabulary'] == 1) {
                        $rules[sprintf('%s.sector_code', $formBase)] = 'required_with:' . sprintf('%s.narrative.%s.narrative', $formBase, $narrativeIndex);
                    }
                    if ($sector['sector_vocabulary'] == 2) {
                        $rules[sprintf('%s.sector_category_code', $formBase)] = 'required_with:' . sprintf('%s.narrative.%s.narrative', $formBase, $narrativeIndex);
                    }
                } else {
                    $rules[sprintf('%s.sector_text', $formBase)] = 'required_with:' . sprintf('%s.narrative.%s.narrative', $formBase, $narrativeIndex);
                }
            }
        }

        return $rules;
    }

    /**
     * Get messages for transaction's sector element
     * @param $formFields
     * @param $formBase
     * @return array
     */
    public function getMessagesForTransactionSectorNarrative($sector, $formFields, $formBase)
    {
        $messages                                                 = [];
        $messages[sprintf('%s.narrative.unique_lang', $formBase)] = 'Languages should be unique.';
        foreach ($formFields as $narrativeIndex => $narrative) {
            $messages[sprintf(
                '%s.narrative.%s.narrative.required_with_language',
                $formBase,
                $narrativeIndex
            )] = 'Narrative is required with language.';

            if ($narrative['narrative'] != "") {
                $messages[sprintf('%s.sector_vocabulary.required_with', $formBase)] = 'Sector Vocabulary is required with Narrative.';
                if ($sector['sector_vocabulary'] == 1 || $sector['sector_vocabulary'] == 2) {
                    if ($sector['sector_vocabulary'] == 1) {
                        $messages[sprintf('%s.sector_code.required_with', $formBase)] = 'Sector Code is required with Narrative.';
                    }
                    if ($sector['sector_vocabulary'] == 2) {
                        $messages[sprintf('%s.sector_category_code.required_with', $formBase)] = 'Sector Code is required with Narrative.';
                    }
                } else {
                    $messages[sprintf('%s.sector_text.required_with', $formBase)] = 'Sector Code is required with Narrative.';
                }
            }
        }

        return $messages;
    }

    /**
     * returns rules for narrative
     * @param      $formFields
     * @param      $formBase
     * @return array
     */
    public function getRulesForResultNarrative($formFields, $formBase)
    {
        $rules                                       = [];
        $rules[sprintf('%s.narrative', $formBase)][] = 'unique_lang';
        $rules[sprintf('%s.narrative', $formBase)][] = 'unique_default_lang';
        foreach ($formFields as $narrativeIndex => $narrative) {
            $rules[sprintf('%s.narrative.%s.narrative', $formBase, $narrativeIndex)][] = 'required';
        }

        return $rules;
    }

    /**
     * returns rules for period start form
     * @param $formFields
     * @param $formBase
     * @return array
     */
    public function getRulesForPeriodStart(
        $formFields,
        $formBase
    ) {
        $rules = [];
        foreach ($formFields as $periodStartKey => $periodStartVal) {
            $rules[$formBase . '.period_start.' . $periodStartKey . '.date'] = 'required|date';
        }

        return $rules;
    }

    /**
     * returns messages for period start form
     * @param $formFields
     * @param $formBase
     * @return array
     */
    public function getMessagesForPeriodStart(
        $formFields,
        $formBase
    ) {
        $messages = [];
        foreach ($formFields as $periodStartKey => $periodStartVal) {
            $messages[$formBase . '.period_start.' . $periodStartKey . '.date.required'] = 'Period Start is required';
            $messages[$formBase . '.period_end.' . $periodStartKey . '.date.date']       = 'Period Start is not a valid date.';
        }

        return $messages;
    }

    /**
     * returns rules for period end form
     * @param $formFields
     * @param $formBase
     * @return array
     */
    public function getRulesForPeriodEnd(
        $formFields,
        $formBase
    ) {
        $rules = [];

        foreach ($formFields as $periodEndKey => $periodEndVal) {
            $rules[$formBase . '.period_end.' . $periodEndKey . '.date'][] = 'required';
            $rules[$formBase . '.period_end.' . $periodEndKey . '.date'][] = 'date';
            $rules[$formBase . '.period_end.' . $periodEndKey . '.date'][] = sprintf(
                'after:%s',
                $formBase . '.period_start.' . $periodEndKey . '.date'
            );
        }

        return $rules;
    }

    /**
     * returns messages for period end form
     * @param $formFields
     * @param $formBase
     * @return array
     */
    public function getMessagesForPeriodEnd(
        $formFields,
        $formBase
    ) {
        $messages = [];

        foreach ($formFields as $periodEndKey => $periodEndVal) {
            $messages[$formBase . '.period_end.' . $periodEndKey . '.date.required'] = 'Period End is required.';
            $messages[$formBase . '.period_end.' . $periodEndKey . '.date.date']     = 'Period End is not a valid date.';
            $messages[$formBase . '.period_end.' . $periodEndKey . '.date.after']    = 'Period End must be a date after Period Start';
        }

        return $messages;
    }
}
