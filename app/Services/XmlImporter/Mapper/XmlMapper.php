<?php namespace App\Services\XmlImporter\Mapper;

/**
 * Class XmlMapper
 * @package App\Services\XmlImporter\Mapper
 */
abstract class XmlMapper
{
    /**
     * Get the name of the current Xml element.
     *
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
     * Get the values of the current Xml element.
     *
     * @param array $element
     * @return string
     */
    protected function value(array $element)
    {
        return getVal($element, ['value'], []);
    }

    /**
     * Get the attributes of the current Xml element.
     *
     * @param array $element
     * @param null  $key
     * @return mixed|string
     */
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
//
//        return '';

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
//    }
}
