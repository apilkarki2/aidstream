<?php namespace App\Services\XmlImporter\Mapper\V103\Activity;

class Activity
{
    protected $activities = [];

    public function map(array $activityData, $template)
    {
        $title = [];

        // Only Title mapped.

        foreach ($activityData as $activity) {
            $elementName    = $this->name($activity);

            if ($elementName == 'title') {
                foreach ($this->value($activity) as $subElement) {
                    $title              = $template['title'];
                    $title['narrative'] = $this->value($subElement);
                    $title['language']  = $this->attributes($subElement, 'language');
                }
            }
        }

        return $title;
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

        return '';
    }
}
