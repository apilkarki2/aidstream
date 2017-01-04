<?php namespace App\Lite\Services\Data\V202\Settings;

use App\Lite\Services\Data\Contract\MapperInterface;

/**
 * Class SettingsData
 * @package App\Lite\Services\Data\Settings
 */
class Settings implements MapperInterface
{

    /**
     * Default IATI version for lite
     */
    const DEFAULT_VERSION = 2.02;

    /**
     * Default Publishing type for lite
     */
    const DEFAULT_PUBLISHING_TYPE = "unsegmented";

    /**
     * Raw data holder for Settings entity.
     *
     * @var array
     */
    protected $rawData = [];

    /**
     * Data template for Settings.
     *
     * @var array
     */
    protected $template = [];

    /**
     * Default Field Groups Template
     *
     * @var array
     */
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

    /**
     * @var
     */
    protected $orgId;

    /**
     * SettingsData constructor.
     *
     * @param array $rawData
     */
    public function __construct(array $rawData)
    {
        $this->rawData = $rawData;
        $this->orgId   = auth()->user()->org_id;
    }

    /**
     * {@inheritdoc}
     */
    public function map()
    {
        $settings['settings'] = [
            'publishing_type'      => self::DEFAULT_PUBLISHING_TYPE,
            'registry_info'        => [
                [
                    'publisher_id'  => getVal($this->rawData, ['publisherId'], ''),
                    'api_id'        => getVal($this->rawData, ['apiKey'], ''),
                    'publish_files' => getVal($this->rawData, ['publishFiles'], 'no')
                ]
            ],
            'default_field_values' => [
                [
                    'default_currency' => getVal($this->rawData, ['defaultCurrency'], ''),
                    'default_language' => getVal($this->rawData, ['defaultLanguage'], '')
                ]
            ],
            'default_field_groups' => $this->defaultFieldGroups,
            'version'              => self::DEFAULT_VERSION,
            'organization_id'      => $this->orgId
        ];

        $settings['organisation'] = [
            'reporting_org' => [
                [
                    'reporting_organization_identifier' => getVal($this->rawData, ['organisationIdentifier'], ''),
                    "reporting_organization_type"       => getVal($this->rawData, ['organisationType'], ''),
                    "narrative"                         => [
                        [
                            "narrative" => getVal($this->rawData, ['organisationName'], ''),
                            "language"  => getVal($this->rawData, ['language'], '')
                        ]
                    ]
                ]
            ]
        ];

        return $settings;
    }

    /**
     * Map database data into frontend compatible format.
     *
     * @return array
     */
    public function reverseMap()
    {
        $formModel = [
            'organisationName'       => getVal($this->rawData, ['reporting_org', 0, 'narrative', 0, 'narrative'], ''),
            'language'               => getVal($this->rawData, ['reporting_org', 0, 'narrative', 0, 'language'], ''),
            'organisationIdentifier' => getVal($this->rawData, ['reporting_org', 0, 'reporting_organization_identifier'], ''),
            'organisationType'       => getVal($this->rawData, ['reporting_org', 0, 'reporting_organization_type'], ''),
            'publisherId'            => getVal($this->rawData, ['registry_info', 0, 'publisher_id'], ''),
            'apiKey'                 => getVal($this->rawData, ['registry_info', 0, 'api_id'], ''),
            'publishFile'            => getVal($this->rawData, ['registry_info', 0, 'publish_files'], ''),
            'defaultCurrency'        => getVal($this->rawData, ['default_field_values', 0, 'default_currency'], ''),
            'defaultLanguage'        => getVal($this->rawData, ['default_field_values', 0, 'default_language'], '')
        ];

        return $formModel;
    }
}
