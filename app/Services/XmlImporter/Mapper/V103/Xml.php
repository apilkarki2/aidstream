<?php namespace App\Services\XmlImporter\Mapper\V103;

use App\Services\XmlImporter\Mapper\V103\Activity\Activity;
use App\Services\XmlImporter\Mapper\V103\Activity\Elements\Transaction;
use App\Services\XmlImporter\Mapper\XmlMapper;

/**
 * Class Xml
 * @package App\Services\XmlImporter\Mapper\V103
 */
class Xml extends XmlMapper
{
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
     * @var array
     */
    protected $activityElements = [
        'reportingOrg',
        'iatiIdentifier',
        'title',
        'description',
        'activityStatus',
        'activityDate',
        'participatingOrg',
        'recipientCountry',
        'recipientRegion',
        'sector',
        'collaborationType',
        'defaultFlowType',
        'defaultFinanceType',
        'defaultAidType',
        'defaultTiedStatus',
        'budget'
    ];

    /**
     * Xml constructor.
     * @param Activity    $activity
     * @param Transaction $transaction
     */
    public function __construct(Activity $activity, Transaction $transaction)
    {
        $this->activity           = $activity;
        $this->transactionElement = $transaction;
    }

    /**
     * Map raw Xml data into AidStream database compatible data for import.
     *
     * @param array $xmlData
     * @param       $template
     * @return array
     */
    public function map(array $xmlData, $template)
    {
        $mappedData = [];

        foreach ($xmlData as $index => $data) {
            $mappedData[$index]['title'][]    = $this->activity->map($this->filter($data, 'iatiActivity'), $template);
            $mappedData[$index]['transaction'][] = $this->transactionElement->map($this->filter($data, 'transaction'), $template);
        }

        dd($mappedData);
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
}
