<?php namespace App\Services\XmlImporter\Mapper;

class XmlProcessor
{
    use XmlImportFactory;

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
     * @var
     */
    protected $xmlMapper;

    /**
     * Xml constructor.
     * @param Template $template
     */
    public function __construct(Template $template)
    {
        $this->template  = $template;
    }

    public function process(array $xml, $version)
    {
        $this->xmlMapper = $this->initializeMapper($version);

        $this->xmlMapper->map($xml, $this->template->loadFor());

        dd($this->xmlMapper);
//        foreach ($xml as $elements) {
//            list($name, $values, $attributes) = [$this->name($elements), $this->value($elements), $this->attributes($elements)];
//
//            if (method_exists($this, $name)) {
//                $this->$name($values, $attributes);
//            }
//        }

        dd('ssss');
    }

    protected function iatiActivity(array $elements, array $attributes)
    {
        foreach ($elements as $element) {
            $this->title($element)
                 ->transaction($element);
        }
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
}
