<?php namespace App\Services\XmlImporter\Mapper\V103;

use App\Services\XmlImporter\Mapper\V103\Activity\Activity;
use App\Services\XmlImporter\Mapper\V103\Activity\Elements\Result;
use App\Services\XmlImporter\Mapper\V103\Activity\Elements\Transaction;
use App\Services\XmlImporter\Mapper\XmlHelper;
use App\Services\XmlImporter\Mapper\XmlMapper;

/**
 * Class Xml
 * @package App\Services\XmlImporter\Mapper\V103
 */
class Xml extends XmlMapper
{
    use XmlHelper;
    /**
     * @var Activity
     */
    protected $activity;

    /**
     * @var array
     */
    protected $iatiActivity = [];

    /**
     * @var array
     */
    protected $transaction = [];

    /**
     * @var Transaction
     */
    protected $transactionElement;

    /**
     * @var Result
     */
    protected $resultElement;


    /**
     * @var array
     */
    protected $activityElements = [
        'reportingOrg',
        'otherIdentifier',
        'iatiIdentifier',
        'title',
        'description',
        'activityStatus',
        'activityDate',
        'activityScope',
        'contactInfo',
        'participatingOrg',
        'recipientCountry',
        'recipientRegion',
        'sector',
        'collaborationType',
        'defaultFlowType',
        'defaultFinanceType',
        'defaultAidType',
        'defaultTiedStatus',
        'budget',
        'location',
        'plannedDisbursement',
        'countryBudgetItems',
        'documentLink',
        'policyMarker',
        'conditions',
        'legacyData',
        'humanitarianScope',
        'collaborationType',
        'capitalSpend',
        'relatedActivity'
    ];

    /**
     * Xml constructor.
     * @param Activity    $activity
     * @param Transaction $transaction
     * @param Result      $result
     */
    public function __construct(Activity $activity, Transaction $transaction, Result $result)
    {
        $this->activity           = $activity;
        $this->transactionElement = $transaction;
        $this->resultElement      = $result;

    }

    /**
     * Map raw Xml data into AidStream database compatible data for import.
     *
     * @param array $activities
     * @param       $template
     * @return array
     * @internal param array $xmlData
     */
    public function map(array $activities, $template)
    {
        $mappedData = [];

        foreach ($activities as $index => $activity) {
            $mappedData[$index]                         = $this->activity->map($this->filter($activity, 'iatiActivity'), $template);
            $mappedData[$index]['default_field_values'] = $this->defaultFieldValues($activity, $template);
            $mappedData[$index]['transactions']         = $this->transactionElement->map($this->filter($activity, 'transaction'), $template);
            $mappedData[$index]['result']               = $this->resultElement->map($this->filter($activity, 'result'), $template);
        }

        dd($mappedData);
    }

    protected function defaultFieldValues($activity, $template)
    {
        $defaultFieldValues                      = $template['default_field_values'];
        $defaultFieldValues['default_currency']  = $this->attributes($activity, 'default-currency');
        $defaultFieldValues['default_language']  = $this->attributes($activity, 'language');
        $defaultFieldValues['default_hierarchy'] = $this->attributes($activity, 'hierarchy');
        $defaultFieldValues['linked_data_uri']   = $this->attributes($activity, 'linked-data-uri');
        $defaultFieldValues['humanitarian']      = $this->attributes($activity, 'humanitarian');

        return $defaultFieldValues;
    }

    /**
     * Filter raw Xml data for a certain element with a specific elementName.
     *
     * @param $xmlData
     * @param $elementName
     */
    protected function filter($xmlData, $elementName)
    {
        foreach ($this->value($xmlData) as $subElement) {
            if ($elementName == 'transaction') {
                $this->filterForTransactions($subElement, $elementName);
            } elseif ($elementName == 'result') {
                $this->filterForResults($subElement, $elementName);
            } elseif ($elementName == 'iatiActivity') {
                $this->filterForActivity($subElement, $elementName);
            }
        }

        return $this->{$elementName};
    }

    /**
     * Filter data for Activity Elements.
     *
     * @param $subElement
     * @param $elementName
     */
    protected function filterForActivity($subElement, $elementName)
    {
        if (in_array($this->name($subElement), $this->activityElements)) {
            $this->{$elementName}[] = $subElement;
        }
    }

    /**
     * Filter data for Transactions Elements.
     *
     * @param $subElement
     * @param $elementName
     */
    protected function filterForTransactions($subElement, $elementName)
    {
        if ($this->name($subElement) == $elementName) {
            $this->{$elementName}[] = $subElement;
        }
    }

    /**
     * @param $subElement
     * @param $elementName
     */
    protected function filterForResults($subElement, $elementName)
    {
        if ($this->name($subElement) == $elementName) {
            $this->{$elementName}[] = $subElement;
        }
    }
}
