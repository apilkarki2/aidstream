<?php namespace App\Lite\Services\Validation\Traits;


use Illuminate\Support\Facades\Validator;

/**
 * Class RegisterValidationRules
 * @package App\Lite\Services\Validation\Traits
 */
trait RegisterValidationRules
{
    /**
     * Contains custom Rules
     */
    protected function customRules()
    {
        Validator::extend(
            'no_spaces',
            function ($attribute, $value, $parameters, $validator) {
                if (preg_match('/\s/', $value)) {
                    return false;
                }

                return true;
            }
        );
    }
}

