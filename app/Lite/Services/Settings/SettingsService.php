<?php namespace App\Lite\Services\Settings;

use App\Lite\Repositories\Organisation\OrganisationRepository;
use App\Lite\Repositories\Settings\SettingsRepository;

class SettingsService
{

    /**
     * @var OrganisationRepository
     */
    protected $organisationRepository;
    /**
     * @var SettingsRepository
     */
    protected $settingsRepository;

    protected $defaultFieldGroups = [
        [
            "Basic Activity Information"  => [
                "title"           => "Title",
                "description"     => "Description",
                "activity_status" => "Activity Status",
                "activity_date"   => "Activity Date"
            ],
            "Participating Organizations" => [
                "participating_organization" => "Participating Organisation"
            ],
            "Geopolitical Information"    => [
                "recipient_country" => "Recipient Country"
            ],
            "Classifications"             => [
                "sector" => "Sector"
            ],
            "Financial"                   => [
                "budget"      => "Budget",
                "transaction" => "Transaction"
            ]
        ]
    ];

    const DEFAULT_VERSION = 2.02;

    const DEFAULT_PUBLISHING_TYPE = "unsegmented";

    protected $orgId;

    public function __construct(OrganisationRepository $organisationRepository, SettingsRepository $settingsRepository)
    {
        $this->organisationRepository = $organisationRepository;
        $this->settingsRepository     = $settingsRepository;
        $this->orgId                  = auth()->user()->org_id;
    }

    public function getSettingsModel()
    {
        $orgId        = auth()->user()->org_id;
        $organisation = json_decode($this->organisationRepository->getOrg($orgId), true);
        $settings     = json_decode($this->settingsRepository->getSettingsWithOrgId($orgId), true);

        $model         = array_merge($organisation, $settings);
        $filteredModel = $this->filterSettingsModel($model);

        return $filteredModel;
    }

    protected function filterSettingsModel($model)
    {
        $filteredModel = [];

        $filteredModel['organisationName']       = getVal($model, ['reporting_org', 0, 'narrative', 0, 'narrative'], '');
        $filteredModel['language']               = getVal($model, ['reporting_org', 0, 'narrative', 0, 'language'], '');
        $filteredModel['organisationIdentifier'] = getVal($model, ['reporting_org', 0, 'reporting_organization_identifier'], '');
        $filteredModel['organisationType']       = getVal($model, ['reporting_org', 0, 'reporting_organization_type'], '');
        $filteredModel['publisherId']            = getVal($model, ['registry_info', 0, 'publisher_id'], '');
        $filteredModel['apiKey']                 = getVal($model, ['registry_info', 0, 'api_id'], '');
        $filteredModel['publishFile']            = getVal($model, ['registry_info', 0, 'publish_files'], '');
        $filteredModel['defaultCurrency']        = getVal($model, ['default_field_values', 0, 'default_currency'], '');
        $filteredModel['defaultLanguage']        = getVal($model, ['default_field_values', 0, 'default_language'], '');

        return $filteredModel;
    }

    public function store($all)
    {
        dd($all);

        $settings     = $this->transform($all);
        $organisation = $this->transformOrg($all);

        $this->settingsRepository->store($settings);
        $this->organisationRepository->store($organisation);

        return;
    }

    protected function transform($all)
    {
        $settings['publishing_type']      = self::DEFAULT_PUBLISHING_TYPE;
        $settings['registry_info']        = [
            'publisher_id'  => getVal($all, ['publisherId'], ''),
            'api_id'        => getVal($all, ['apiKey'], ''),
            'publish_files' => getVal($all, ['publishFiles'], 'no')
        ];
        $settings['default_field_values'] = [
            'default_currency' => getVal($all, ['defaultCurrency'], ''),
            'default_language' => getVal($all, ['defaultLanguage'], '')
        ];
        $settings['default_field_groups'] = $this->defaultFieldGroups;
        $settings['version']              = self::DEFAULT_VERSION;
        $settings['organization_id']      = $this->orgId;

        return $settings;
    }

    protected function transformOrg($all)
    {
        $org['reporting_org'] = [
            'reporting_organization_identifier' => getVal($all, ['organisationIdentifier'], ''),
            "reporting_organization_type"       => getVal($all, ['organisationType'], ''),
            "narrative"                         => [
                [
                    "narrative" => getVal($all, ['organisationName'], ''),
                    "language"  => getVal($all, ['language'])
                ]
            ]
        ];

        return $org;
    }
}