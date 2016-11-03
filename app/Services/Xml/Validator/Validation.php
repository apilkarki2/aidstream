<?php namespace App\Services\Xml\Validator;

use App\Services\Xml\Validator\Traits\RegistersValidationRules;
use Illuminate\Validation\Factory;
use Symfony\Component\Translation\TranslatorInterface;


/**
 * Class Validation
 * @package App\Services\Xml\Validator
 */
class Validation extends Factory
{
    use RegistersValidationRules;

    /**
     * @var
     */
    protected $validator;

    /**
     * Validation constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->registerValidationRules();
    }

    /**
     * Initialize the validator object.
     *
     * @param $activity
     * @param $rules
     * @param $messages
     * @return $this
     */
    public function initialize($activity, $rules, $messages)
    {
        $this->validator = $this->make($activity, $rules, $messages);

        return $this;
    }

    /**
     * Run the validator and check if it passes.
     *
     * @return $this
     */
    public function passes()
    {
        $this->validator->passes();

        return $this;
    }

    /**
     * Get the unique validation errors.
     *
     * @param      $activityId
     * @param bool $shouldBeUnique
     * @return array
     */
    public function withErrors($activityId, $shouldBeUnique = false)
    {
        $errors = [];

        foreach ($this->errors() as $index => $error) {
            $element                  = $this->parseErrors($index);
            $errors[$element][$index] = getVal($error, [0], '');
        }

        $errors = $this->embedLinks($activityId, $errors);

        if ($shouldBeUnique) {
            $errors = $this->getDistinctErrors($errors);
        }

        return $errors;
    }

    /**
     * Parse the errors from the validator.
     *
     * @param $index
     * @return string
     */
    protected function parseErrors($index)
    {
        $element = getVal(explode('.', $index), [0], '');

        return ucwords(((str_replace('_', ' ', $element))));
    }

    /**
     * Get the Validator error messages.
     *
     * @return mixed
     */
    protected function errors()
    {
        return $this->validator->errors()->getMessages();
    }

    /**
     * Returns rules for narrative.
     *
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
     * Returns messages for narrative.
     *
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
     * Returns rules for narrative if narrative is required.
     *
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
     * Get message for narrative.
     *
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
     * Get rules for transaction's sector element.
     *
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
     * Get messages for transaction's sector element.
     *
     * @param $sector
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
     * Returns rules for narrative.
     *
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
     * Returns rules for period start form.
     *
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
     * Returns messages for period start form.
     *
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
     * Returns rules for period end form.
     *
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
     * Returns messages for period end form.
     *
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

    /**
     * Get distinct errors from a list of errors.
     *
     * @param $errors
     * @return mixed
     */
    protected function getDistinctErrors($errors)
    {
        foreach ($errors as $key => $error) {
            $errors[$key] = array_unique($error);
        }

        return $errors;
    }

    /**
     * Embed Links to the respective elements for the error messages.
     *
     * @param       $activityId
     * @param array $errors
     * @return array
     */
    protected function embedLinks($activityId, array $errors)
    {
        $links = [];

        foreach ($errors as $element => $error) {
            if ($element != 'Conditions') {
                $elementUri                 = preg_replace("/[\s]/", "-", strtolower($element));
                $link                       = route('activity.' . $elementUri . '.index', $activityId);
                $links[$element]['link']    = $link;
                $links[$element]['message'] = reset($error);
            }
        }

        return $links;
    }
}
