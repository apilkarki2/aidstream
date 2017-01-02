<?php

//Form validation errors, importer validations
return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    "accepted"                                  => "The :attribute must be accepted.-esp",
    "active_url"                                => "The :attribute is not a valid URL.-esp",
    "after"                                     => "The :attribute must be a date after :date.-esp",
    "alpha"                                     => "The :attribute may only contain letters.-esp",
    "alpha_dash"                                => "The :attribute may only contain letters, numbers, and dashes.-esp",
    "alpha_num"                                 => "The :attribute may only contain letters and numbers.-esp",
    "array"                                     => "The :attribute must be an array.-esp",
    "before"                                    => "The :attribute must be a date before :date.-esp",
    "between"                                   => [
        "numeric" => "The :attribute must be between :min and :max.-esp",
        "file"    => "The :attribute must be between :min and :max kilobytes.-esp",
        "string"  => "The :attribute must be between :min and :max characters.-esp",
        "array"   => "The :attribute must have between :min and :max items.-esp",
    ],
    "boolean"                                   => "The :attribute field must be true or false.-esp",
    "confirmed"                                 => "The :attribute confirmation does not match.-esp",
    "date"                                      => "The :attribute is not a valid date.-esp",
    "date_format"                               => "The :attribute does not match the format :format.-esp",
    "different"                                 => "The :attribute and :other must be different.-esp",
    "digits"                                    => "The :attribute must be :digits digits.-esp",
    "digits_between"                            => "The :attribute must be between :min and :max digits.-esp",
    "email"                                     => "The :attribute must be a valid email address.-esp",
    "filled"                                    => "The :attribute field is required.-esp",
    "exists"                                    => "The selected :attribute is invalid.-esp",
    "image"                                     => "Please select an image file.-esp",
    "in"                                        => "The selected :attribute is invalid.-esp",
    "integer"                                   => "The :attribute must be an integer.-esp",
    "ip"                                        => "The :attribute must be a valid IP address.-esp",
    "max"                                       => [
        "numeric" => "The :attribute may not be greater than :max.-esp",
        "file"    => "The :attribute may not be greater than :max kilobytes.-esp",
        "string"  => "The :attribute may not be greater than :max characters.-esp",
        "array"   => "The :attribute may not have more than :max items.-esp",
    ],
    "mimes"                                     => "The :attribute must be a file of type: :values.-esp",
    "min"                                       => [
        "numeric" => "The :attribute must be at least :min.-esp",
        "file"    => "The :attribute must be at least :min kilobytes.-esp",
        "string"  => "The :attribute must be at least :min characters.-esp",
        "array"   => "The :attribute must have at least :min items.-esp",
    ],
    'unique_validation'                         => 'The :attribute is invalid and must be unique.-esp',
    "not_in"                                    => "The selected :attribute is invalid.-esp",
    "numeric"                                   => "The :attribute must be a number.-esp",
    "regex"                                     => "Only %attribute, letters and numbers are allowed.-esp",
    "required"                                  => ":attribute is required.-esp",
    "required_if"                               => "The :attribute field is required when :values is :value.-esp",
    "required_with"                             => "The :attribute is required with :values.-esp",
    "required_with_all"                         => "The :attribute field is required when :values is present.-esp",
    "required_without"                          => "The :attribute field is required when :values is not present.-esp",
    "required_without_all"                      => "The :attribute field is required when none of :values are present.-esp",
    "same"                                      => "The :attribute and :other must match.-esp",
    "size"                                      => [
        "numeric" => "The :attribute must be :size.-esp",
        "file"    => "The :attribute must be :size kilobytes.-esp",
        "string"  => "The :attribute must be :size characters.-esp",
        "array"   => "The :attribute must contain :size items.-esp",
    ],
    "unique"                                    => "The :attribute should be unique.-esp",
    "url"                                       => "'Enter valid URL. eg. http://example.com-esp",
    "timezone"                                  => "The :attribute must be a valid zone.-esp",
    /* Custom validation messages */
    "exclude_operators"                         => "Symbols are not allowed.-esp",
    "unique_default_lang"                       => 'Leaving language empty is same as selecting default language. Default language ":language" has already been selected.-esp',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */
    'sum'                                       => 'Total percentage of :attribute under the same vocabulary must be equal to 100.-esp',
    'required_custom'                           => ':attribute is required when there are multiple codes.-esp',
    'total'                                     => ':attribute should be 100 when there is only one :values.-esp',
    'csv_required'                              => 'At row :number :attribute is required-esp',
    'csv_unique'                                => 'At row :number :attribute should be unique-esp',
    'csv_invalid'                               => 'At row :number :attribute is invalid-esp',
    'csv_numeric'                               => 'At row :number :attribute should be numeric-esp',
    'csv_unique_validation'                     => 'At row :number :attribute is invalid and must be unique.-esp',
    'csv_among'                                 => 'At row :number at least one :type among :attribute is required.-esp',
    'csv_only_one'                              => 'At row :number only one among :attribute is required.-esp',
    'year_value_narrative_validation'           => ':year and :value is required if :narrative is not empty.-esp',
    'year_narrative_validation'                 => ':year is required if :narrative is not empty.-esp',
    'org_required'                              => 'At least one organisation name is required-esp',
    'custom_unique'                             => ':attribute has already been taken.-esp',
    'user_identifier_taken'                     => 'Sorry! this User Identifier is already taken-esp',
    'enter_valid'                               => 'Please enter valid :attribute-esp',
    'sector_validation'                         => 'Sector must be present either at Activity or in all Transactions level.-esp',
    'transaction_sector_validation'             => 'All Transactions must contain Sector element.-esp',
    'sector_in_activity_and_transaction_remove' => 'You can only mention Sector either at Activity or in Transaction level(should be included in all transactions) but not both. <br/>Please click the link to remove Sector From: <a href=\'%s\' class=\'delete_data\'>Transaction Level</a> OR <a href=\'%s\' class=\'delete_data\'>Activity Level</a>-esp',
    'sector_in_activity_and_transaction'        => 'You can only mention Recipient Country or Region either in Activity Level or in Transaction level. You can\'t have Country/Region in both Activity level and Transaction level.-esp',
    'recipient_country_or_region_required'      => 'Either Recipient Country or Recipient Region is required-esp',
    'sum_of_percentage'                         => 'The sum of percentage in :attribute must be 100.-esp',
    'validation_before_completed'               => 'Please make sure you enter the following fields before changing to completed state.-esp',
    'reporting_org_identifier_unique'           => 'This reporting organization identifier is being used by :orgName. This identifier has to be unique. Please contact us at support@aidstream.org-esp',
    'code_list'                                 => ':attribute is not valid.-esp',
    'string'                                    => ':attribute should be string-esp',
    'negative'                                  => ':attribute cannot be negative-esp',
    'actual_date'                               => 'Actual Start Date And Actual End Date must not exceed present date-esp',
    'multiple_activity_date'                    => 'Multiple Activity dates are not allowed.-esp',
    'start_end_date'                            => 'Actual Start Date or Planned Start Date should be before Actual End Date or Planned End Date.-esp',
    'csv_date'                                  => ':attribute must be of format Y-m-d.-esp',
    'multiple_values'                           => 'Multiple :attribute are not allowed.-esp',
    'csv_size'                                  => 'At least one :attribute is required-esp',
    'multiple_narratives'                       => 'Multiple narratives for :attribute with the same type is not allowed.-esp',
    'funding_implementing_required'             => 'There should be at least one participating organization with the role "Funding"(id:1) or "Implementing"(id:4).-esp',
    'required_only_one_among'                   => 'Either :attribute or :values is required.-esp',
    'recipient_country_region_percentage_sum'   => 'Sum of percentage of Recipient Country and Recipient Region must be equal to 100.-esp',
    'invalid_in_transaction'                    => 'Entered :attribute is incorrect in Transaction.-esp',
    'required_if_in_transaction'                => ':attribute is required if :values is not present in Transaction.-esp',
    'sector_vocabulary_required'                => 'Sector Vocabulary is required in Transaction if not present in Activity Level.-esp',
    'required_in_transaction'                   => ':attribute is required in Transaction.-esp',
    'invalid_language'                          => 'Invalid :attribute language-esp',
    'unique_lang'                               => 'Repeated :attribute in the same language is not allowed.-esp',
    'indicator_ascending'                       => 'Indicator Ascending should be true/false, 0/1 or Yes/No.-esp',
    'indicator_size'                            => 'Indicator Baseline Year or Value should occur once and no more than once within an Indicator.-esp',
    'narrative_required'                        => ':attribute Narrative is required.-esp',
    'no_more_than_once'                         => ':attribute should occur once and no more than once within :values.-esp',
    'custom'                                    => [
        'attribute-name' => [
            'rule-name' => 'custom-message-esp',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
