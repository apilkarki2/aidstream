<?php namespace App\Lite\Services\Validation\Rules\V202;

use App\Core\V201\Traits\GetCodes;

/**
 * Class Activity
 * @package App\Lite\Services\Validation\Rules\V202
 */
class Activity
{
    use GetCodes;
    /**
     * @var array
     */
    protected $activityRules = [];
    /**
     * @var array
     */
    protected $methods = [
        'ActivityIdentifier',
        'ActivityTitle',
        'GeneralDescription',
        'ActivityStatus',
        'Sector',
        'StartDate',
        'EndDate',
        'Country',
        'OrganisationName',
        'OrganisationType'
    ];
    /**
     * @var array
     */
    protected $activityMessages = [];

    /**
     * @return array
     */
    public function rules()
    {
        foreach ($this->methods() as $method) {
            $methodName = sprintf('rulesFor%s', $method);

            if (method_exists($this, $methodName)) {
                $this->{$methodName}();
            }
        }

        return $this->activityRules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        foreach ($this->methods() as $method) {
            $methodName = sprintf('messagesFor%s', $method);

            if (method_exists($this, $methodName)) {
                $this->{$methodName}();
            }
        }

        return $this->activityMessages;
    }

    /**
     * @return array
     */
    protected function methods()
    {
        return $this->methods;
    }

    /**
     *
     */
    protected function rulesForActivityIdentifier()
    {
        $this->activityRules['activity_identifier'] = 'required';
    }

    protected function messagesForActivityIdentifier()
    {
        $this->activityMessages['activity_identifier.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.activity_identifier')]);
    }

    /**
     *
     */
    protected function rulesForActivityTitle()
    {
        $this->activityRules['activity_title'] = 'required';
    }

    protected function messagesForActivityTitle()
    {
        $this->activityMessages['activity_title.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.activity_title')]);
    }

    /**
     *
     */
    protected function rulesForGeneralDescription()
    {
        $this->activityRules['general_description'] = 'required';
    }

    protected function messagesForGeneralDescription()
    {
        $this->activityMessages['general_description.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.general_description')]);
    }

    /**
     *
     */
    protected function rulesForActivityStatus()
    {
        $this->activityRules['activity_status'] = sprintf('required|in%s', $this->getStringFormatCode('ActivityStatus', 'Activity'));
    }

    protected function messagesForActivityStatus()
    {
        $this->activityMessages['activity_status.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.general_description')]);
        $this->activityMessages['activity_status.in']       = trans('validation.code_list', ['attribute' => trans('lite/elementForm.general_description')]);
    }

    protected function rulesForSector()
    {
        $this->activityRules['sector'] = sprintf('required|in:%s', $this->getStringFormatCode('Sector', 'Activity'));
    }

    protected function messagesForSector()
    {
        $this->activityMessages['sector.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.sector')]);
        $this->activityMessages['sector.in']       = trans('validation.code_list', ['attribute' => trans('lite/elementForm.sector')]);
    }

    protected function rulesForStartDate()
    {
        $this->activityRules['start_date'] = 'required|date';
    }

    protected function messagesForStartDate()
    {
        $this->activityMessages['start_date.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.start_date')]);
        $this->activityMessages['start_date.date']     = trans('validation.date', ['attribute' => trans('lite/elementForm.start_date')]);
    }

    protected function rulesForEndDate()
    {
        $this->activityRules['end_date'] = 'required|date|after:start_date';
    }

    protected function messagesForEndDate()
    {
        $this->activityMessages['end_date.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.end_date')]);
        $this->activityMessages['end_date.date']     = trans('validation.date', ['attribute' => trans('lite/elementForm.end_date')]);
        $this->activityMessages['end_date.after']    = trans('validation.after', ['attribute' => trans('lite/elementForm.end_date'), 'date' => trans('lite/elementForm.start_date')]);
    }

    protected function rulesForCountry()
    {
        $this->activityRules['country'] = sprintf('required|in:%s', $this->getStringFormatCode('Country', 'Organization'));
    }

    protected function messagesForCountry()
    {
        $this->activityMessages['country.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.country')]);
        $this->activityMessages['country.in']       = trans('validation.code_list', ['attribute' => trans('lite/elementForm.country')]);
    }

    protected function rulesForOrganisationName()
    {
        $this->activityRules['funding_organisations.*.organisation_name']      = 'required';
        $this->activityRules['implementing_organisations.*.organisation_name'] = 'required';
    }

    protected function messagesForOrganisationName()
    {
        $this->activityMessages['funding_organisations.*.organisation_name.required']      = trans('validation.required', ['attribute' => trans('lite/elementForm.organisation_name')]);
        $this->activityMessages['implementing_organisations.*.organisation_name.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.organisation_name')]);
    }

    protected function rulesForOrganisationType()
    {
        $this->activityRules['funding_organisations.*.organisation_type']      = sprintf('required|in:%s', $this->getStringFormatCode('OrganizationType', 'Organization'));
        $this->activityRules['implementing_organisations.*.organisation_type'] = sprintf('required|in:%s', $this->getStringFormatCode('OrganizationType', 'Organization'));
    }

    protected function messagesForOrganisationType()
    {
        $this->activityMessages['funding_organisations.*.organisation_type.required']      = trans('validation.required', ['attribute' => trans('lite/elementForm.funding_organisation')]);
        $this->activityMessages['funding_organisations.*.organisation_type.in']            = trans('validation.code_list', ['attribute' => trans('lite/elementForm.funding_organisation')]);
        $this->activityMessages['implementing_organisations.*.organisation_type.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.implementing_organisation')]);
        $this->activityMessages['implementing_organisations.*.organisation_type.in']       = trans('validation.code_list', ['attribute' => trans('lite/elementForm.implementing_organisation')]);
    }
}
