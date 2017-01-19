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
                    $mappedData[$index]['transaction']                                                          = $this->template;
                    $mappedData[$index]['activity_id']                                                          = getVal($this->rawData, ['activity_id'], null);
                    $mappedData[$index]['transaction']['reference']                                             = getVal($value, [$index, 'reference'], null);
                    $mappedData[$index]['transaction']['transaction_type'][0]['transaction_type_code']          = getVal($this->rawData, ['type'], null);
                    $mappedData[$index]['transaction']['transaction_date'][0]['date']                           = getVal($value, [$index, 'date'], null);
                    $mappedData[$index]['transaction']['value'][0]['amount']                                    = getVal($value, [$index, 'amount'], null);
                    $mappedData[$index]['transaction']['value'][0]['currency']                                  = getVal($value, [$index, 'currency'], null);
                    $mappedData[$index]['transaction']['value'][0]['date']                                      = getVal($value, [$index, 'date'], null);
                    $mappedData[$index]['transaction']['description'][0]['narrative'][0]['narrative']           = getVal($value, [$index, 'description'], null);
                    $mappedData[$index]['transaction']['receiver_organization'][0]['narrative'][0]['narrative'] = getVal($value, [$index, 'organisation'], null);
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

        foreach ($this->rawData as $index => $field) {
            $mappedData[$index]['reference']    = getVal($field, ['transaction', 'reference'], '');
            $mappedData[$index]['date']         = getVal($field, ['transaction', 'transaction_date', 0, 'date'], '');
            $mappedData[$index]['amount']       = getVal($field, ['transaction', 'value', 0, 'amount'], '');
            $mappedData[$index]['currency']     = getVal($field, ['transaction', 'value', 0, 'currency'], '');
            $mappedData[$index]['description']  = getVal($field, ['transaction', 'description', 0, 'narrative', 0, 'narrative'], '');
            $mappedData[$index]['organisation'] = getVal($field, ['transaction', 'receiver_organization', 0, 'narrative', 0, 'narrative'], '');
        }

        return $mappedData;
    }

    /**
     * Provides V202 template
     *
     * @return string
     */
    protected function loadTemplate()
    {
        return file_get_contents(app_path('Services/XmlImporter/Foundation/Support/Templates/V202.json'));
    }
}

