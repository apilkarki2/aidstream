<?php namespace App\Services\XmlImporter\Mapper;


use App\Services\XmlImporter\Mapper\V201\Activity\Activity;
use App\Services\XmlImporter\Mapper\V201\Activity\Elements\Transaction;

class Xml
{
    protected $template;
    protected $titles = [];
    protected $transactions = [];

//    protected $xmlKeyMappings = [
//        'transaction' => [
//            'transaction_type_code' => 'code',
//            'date'                  => 'iso-date',
//            'value'   => 'amount',
////            'value'                 => [
////                'amount'   => 'value',
////                'date'     => '',
////                'currency' => ''
////            ]
//        ]
//    ];

    /**
     * @var Activity
     */
    protected $activity;

    /**
     * @var Transaction
     */
    protected $transaction;

    public function __construct(Template $template, Activity $activity, Transaction $transaction)
    {
        $this->template    = $template;
        $this->activity    = $activity;
        $this->transaction = $transaction;
    }

    public function map(array $xml, $version)
    {
        $this->template->loadFor($version);

        foreach ($xml as $elements) {
            list($name, $values, $attributes) = [$this->name($elements), $this->value($elements), $this->attributes($elements)];

            if (method_exists($this, $name)) {
                $this->$name($values, $attributes);
            }
        }

        dd('ssss');
    }

    protected function iatiActivity(array $elements, array $attributes)
    {
        foreach ($elements as $element) {
            $this->title($element)
                 ->transaction($element);
        }
        dd($this->transactions);
        dd($this->titles);
        dd($elements, $attributes);
    }

    protected function name($element, $snakeCase = false)
    {
        if (is_array($element)) {
            $camelCaseString = camel_case(str_replace('{}', '', $element['name']));

            return $snakeCase ? snake_case($camelCaseString) : $camelCaseString;
        }

        $camelCaseString = camel_case(str_replace('{}', '', $element));

        return $snakeCase ? snake_case($camelCaseString) : $camelCaseString;
    }

    protected function value(array $element)
    {
        return getVal($element, ['value'], []);
    }

    protected function attributes(array $element, $key = null)
    {
        if (!$key) {
            return getVal($element, ['attributes'], []);
        }

        $value = getVal($element, ['attributes'], []);

        if ($value && ($key == 'language')) {
            $code = array_first(
                $value,
                function () {
                    return true;
                }
            );

            return $code;

//            $c = array_last(
//                explode(
//                    '}',
//                    array_first(
//                        array_flip($value),
//                        function () {
//                            return true;
//                        }
//                    )
//                ),
//                function () {
//                    return true;
//                }
//            );
        }
    }

    protected function title($element)
    {
        $elementName = $this->name($element);

        if ($elementName == 'title') {
            foreach ($this->value($element) as $subElement) {
                $title              = $this->template->get('title');
                $title['narrative'] = $this->value($subElement);
                $title['language']  = $this->attributes($subElement, 'language');

                $this->titles['title'][] = $title;
            }
        }

        return $this;
    }

//    protected function transaction($element)
//    {
//        $elementName = $this->name($element);
//
//        if ($elementName === 'transaction') {
//            foreach ($this->value($element) as $subElement) {
//                $transaction                                 = $this->template->get('transaction');
//                $value                                       = $transaction[$this->name($subElement, true)];
//                $transaction[$this->name($subElement, true)] = $this->fill($value, $subElement);
//
//
//                if ($this->name($subElement, true) == 'value') {
//                    $transaction[$this->name($subElement, true)] = $this->fill($value, $subElement);
//
////                    dd($element, $value, $transaction);
//                }
//
//                $this->transactions['transaction'][] = $transaction;
//            }
//        }
//    }
//
//    protected function fill($templateValue, $subElement)
//    {
//        if (is_array($templateValue)) {
//            $data = $this->arrayFirst($templateValue);
//
//            if (is_array($data)) {
//                if ($this->name($subElement) == 'value') {
//                    $key        = $this->arrayFirst(array_keys($data));
//                    $data[$key] = $this->fetchValue($subElement, $this->name($subElement));
//
////                    $data[$key] = $this->fetchAttribute($subElement, $this->name($subElement));
//
////                    dd($subElement, $data);
////                    dd($data);
////                    dd($templateValue, $subElement, $this->name($subElement));
//                }
//
//                $key        = $this->arrayFirst(array_keys($data));
//                $data[$key] = $this->fetchAttribute($subElement, $key);
//
//                return $data;
//            }
//        }
//    }

//    protected function arrayFirst($array)
//    {
//        return array_first(
//            $array,
//            function () {
//                return true;
//            }
//        );
//    }
//
//    protected function fetchAttribute($subElement, $attributeName)
//    {
//        try {
//            if (array_key_exists($attributeName, $this->xmlKeyMappings['transaction'])) {
//                return getVal($subElement, ['attributes', $this->xmlKeyMappings['transaction'][$attributeName]], '');
//            }
//        } catch (\Exception $exception) {
//            dd($subElement, $attributeName, $exception);
//        }
//    }
//
//    protected function fetchValue($subElement, $key)
//    {
//        return getVal($subElement, [$key], '');
//    }
}
