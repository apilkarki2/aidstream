<?php namespace App\Services\XmlImporter\Mapper\V103\Activity\Elements;

/**
 * Class Transaction
 * @package App\Services\XmlImporter\Mapper\V103\Activity\Elements
 */
class Transaction
{
    /**
     * @var array
     */
    protected $transaction = [];

    /**
     * @var array
     */
    protected $template = [];

    /**
     * Map raw Xml Transaction data for import.
     *
     * @param array $transactions
     * @param       $template
     * @return array
     */
    public function map(array $transactions, $template)
    {
        $this->template = $template;

        foreach ($transactions as $transaction) {
            $this->reference($transaction);

            foreach ($this->getValue($transaction) as $subElement) {
                $fieldName = $this->name($subElement['name']);
                $this->$fieldName($subElement);
            }
        }

        return $this->transaction;
    }

    /**
     * @param $element
     */
    protected function reference($element)
    {
        $this->transaction['reference'] = getVal($element['attributes'], ['ref']);
    }

    /**
     * @param $subElement
     */
    protected function transactionType($subElement)
    {
        $this->transaction['transaction_type'][0]['transaction_type_code'] = getVal($subElement['attributes'], ['code']);
    }

    /**
     * @param $subElement
     */
    protected function transactionDate($subElement)
    {
        $this->transaction['transaction_date'][0]['date'] = getVal($subElement['attributes'], ['iso-date']);
    }

    /**
     * @param $subElement
     */
    protected function value($subElement)
    {
        $this->transaction['value'][0]['amount']   = getVal($subElement, ['value']);
        $this->transaction['value'][0]['date']     = getVal($subElement['attributes'], ['value-date']);
        $this->transaction['value'][0]['currency'] = getVal($subElement['attributes'], ['currency']);
    }

    /**
     * @param $subElement
     */
    protected function description($subElement)
    {
        $this->narrative($subElement, 'description');
    }

    /**
     * @param $subElement
     */
    protected function providerOrg($subElement)
    {
        $this->transaction['provider_organization'][0]['organization_identifier_code'] = getVal($subElement['attributes'], ['ref']);
        $this->transaction['provider_organization'][0]['type']                         = getVal($subElement['attributes'], ['type']);
        $this->transaction['provider_organization'][0]['provider_activity_id']         = getVal($subElement['attributes'], ['provider-activity-id']);
        $this->narrative($subElement, 'provider_organization');
    }

    /**
     * @param $subElement
     */
    protected function receiverOrg($subElement)
    {
        $this->transaction['receiver_organization'][0]['organization_identifier_code'] = getVal($subElement['attributes'], ['ref']);
        $this->transaction['receiver_organization'][0]['type']                         = getVal($subElement['attributes'], ['type']);
        $this->transaction['receiver_organization'][0]['receiver_activity_id']         = getVal($subElement['attributes'], ['receiver-activity-id']);
        $this->narrative($subElement, 'receiver_organization');
    }

    /**
     * @param $subElement
     */
    protected function disbursementChannel($subElement)
    {
        $this->transaction['disbursement_channel'][0]['disbursement_channel_code'] = getVal($subElement['attributes'], ['code']);
    }

    /**
     * @param $subElement
     */
    protected function sector($subElement)
    {
        $this->transaction['sector'][0]['sector_vocabulary']    = ($vocabulary = getVal($subElement['attributes'], ['vocabulary']));
        $this->transaction['sector'][0]['sector_code']          = ($vocabulary == 1) ? getVal($subElement['attributes'], ['code']) : "";
        $this->transaction['sector'][0]['sector_category_code'] = ($vocabulary == 2) ? getVal($subElement['attributes'], ['code']) : "";
        $this->transaction['sector'][0]['sector_text']          = ($vocabulary != 1 && $vocabulary != 2) ? getVal($subElement['attributes'], ['code']) : "";
        $this->transaction['sector'][0]['vocabulary_uri']       = getVal($subElement['attributes'], ['vocabulary-uri']);
        $this->narrative($subElement, 'sector');
    }

    /**
     * @param $subElement
     */
    protected function recipientCountry($subElement)
    {
        $this->transaction['recipient_country'][0]['country_code'] = getVal($subElement['attributes'], ['code']);
        $this->narrative($subElement, 'recipient_country');
    }

    /**
     * @param $subElement
     */
    protected function recipientRegion($subElement)
    {
        $this->transaction['recipient_region'][0]['region_code']    = getVal($subElement['attributes'], ['code']);
        $this->transaction['recipient_region'][0]['vocabulary']     = getVal($subElement['attributes'], ['vocabulary']);
        $this->transaction['recipient_region'][0]['vocabulary_uri'] = getVal($subElement['attributes'], ['vocabulary-uri']);
        $this->narrative($subElement, 'recipient_region');
    }

    /**
     * @param $subElement
     */
    protected function flowType($subElement)
    {
        $this->transaction['flow_type'][0]['flow_type'] = getVal($subElement['attributes'], ['code']);
    }

    /**
     * @param $subElement
     */
    protected function financeType($subElement)
    {
        $this->transaction['finance_type'][0]['finance_type'] = getVal($subElement['attributes'], ['code']);
    }

    /**
     * @param $subElement
     */
    protected function aidType($subElement)
    {
        $this->transaction['aid_type'][0]['aid_type'] = getVal($subElement['attributes'], ['code']);
    }

    /**
     * @param $subElement
     */
    protected function tiedStatus($subElement)
    {
        $this->transaction['tied_status'][0]['tied_status_code'] = getVal($subElement['attributes'], ['code']);
    }

    /**
     * @param      $element
     * @param bool $snakeCase
     * @return string
     */
    protected function name($element, $snakeCase = false)
    {
        if (is_array($element)) {
            $camelCaseString = camel_case(str_replace('{}', '', $element['name']));

            return $snakeCase ? snake_case($camelCaseString) : $camelCaseString;
        }

        $camelCaseString = camel_case(str_replace('{}', '', $element));

        return $snakeCase ? snake_case($camelCaseString) : $camelCaseString;
    }

    /**
     * @param $subElement
     * @param $field
     */
    protected function narrative($subElement, $field)
    {
        if (is_array(getVal((array) $subElement, ['value'], []))) {
            foreach (getVal((array) $subElement, ['value'], []) as $index => $value) {
                $this->transaction[$field][0]['narrative'][$index] = [
                    'narrative' => getVal($value, ['value']),
                    'language'  => $this->getLanguage($value)
                ];
            }
        } else {
            $this->transaction[$field][0]['narrative'][0] = [
                'narrative' => getVal($subElement, ['value']),
                'language'  => $this->getLanguage($subElement)
            ];
        }
    }

    /**
     * @param array $element
     * @return string
     */
    protected function getValue(array $element)
    {
        return getVal($element, ['value'], []);
    }

    /**
     * @param $value
     * @return string
     */
    protected function getLanguage($value)
    {
        if (is_array($value)) {
            foreach ($value['attributes'] as $key => $lang) {
                return $lang;
            }
        }

        return "";
    }

//    protected function attributes(array $element, $key = null)
//    {
//        if (!$key) {
//            return getVal($element, ['attributes'], []);
//        }
//
//        $value = getVal($element, ['attributes'], []);
//
//        if ($value && ($key == 'language')) {
//            $code = array_first(
//                $value,
//                function () {
//                    return true;
//                }
//            );
//
//            return $code;
//        }
//    }
}
