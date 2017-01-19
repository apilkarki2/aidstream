<?php namespace App\Lite\Services\Data\V202\Transaction;

use App\Lite\Services\Data\Contract\MapperInterface;

/**
 * Class Transaction
 * @package App\Lite\Services\Data\Transaction
 */
class Transaction implements MapperInterface
{

    /**
     * Raw data holder for Transaction entity.
     *
     * @var array
     */
    protected $rawData = [];

    /**
     * Transaction constructor.
     * @param array $rawData
     */
    public function __construct(array $rawData)
    {
        $this->rawData  = $rawData;
        $this->template = getVal(json_decode($this->loadTemplate(), true), ['transaction'], []);
    }

    /**
     * Template for transaction.
     *
     * @var array|string
     */
    protected $template = [];

    /**
     * Map the raw data to element template.
     *
     * @return array
     */
    public function map()
    {
        $mappedData = [];

        foreach ($this->rawData as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $index => $field) {
                    $mappedData['transaction']                                                          = $this->template;
                    $mappedData['activity_id']                                                          = getVal($this->rawData, ['activity_id'], null);
                    $mappedData['transaction']['reference']                                             = getVal($value, [$index, 'reference'], null);
                    $mappedData['transaction']['transaction_type'][0]['transaction_type_code']          = getVal($this->rawData, ['type'], null);
                    $mappedData['transaction']['transaction_date'][0]['date']                           = getVal($value, [$index, 'date'], null);
                    $mappedData['transaction']['value'][0]['amount']                                    = getVal($value, [$index, 'amount'], null);
                    $mappedData['transaction']['value'][0]['currency']                                  = getVal($value, [$index, 'currency'], null);
                    $mappedData['transaction']['value'][0]['date']                                      = getVal($value, [$index, 'date'], null);
                    $mappedData['transaction']['description'][0]['narrative'][0]['narrative']           = getVal($value, [$index, 'description'], null);
                    $mappedData['transaction']['receiver_organization'][0]['narrative'][0]['narrative'] = getVal($value, [$index, 'organisation'], null);
                }
            }
        }

        return $mappedData;
    }

    /**
     * Map database data into frontend compatible format.
     *
     * @return mixed
     */
    public function reverseMap()
    {
        $mappedData = [];

//        foreach (getVal($this->rawData, ['transaction'], []) as $index => $field) {
//            $mappedData['transaction'][$index]['startDate'] = getVal($field, ['period_start', 0, 'date'], '');
//            $mappedData['transaction'][$index]['endDate']   = getVal($field, ['period_end', 0, 'date'], '');
//            $mappedData['transaction'][$index]['amount']    = getVal($field, ['value', 0, 'amount'], '');
//            $mappedData['transaction'][$index]['currency']  = getVal($field, ['value', 0, 'currency'], '');
//        }

        return $mappedData;
    }

    /**
     * @return string
     */
    protected function loadTemplate()
    {
        return file_get_contents(app_path('Services/XmlImporter/Foundation/Support/Templates/V202.json'));
    }
}

