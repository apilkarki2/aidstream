<?php namespace App\Lite\Services\Settings;

use App\Lite\Repositories\Organisation\OrganisationRepository;
use App\Lite\Repositories\Settings\SettingsRepository;
use Illuminate\Database\DatabaseManager;
use Psr\Log\LoggerInterface;

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
    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected $database;

    public function __construct(OrganisationRepository $organisationRepository, SettingsRepository $settingsRepository, DatabaseManager $database, LoggerInterface $logger)
    {
        $this->organisationRepository = $organisationRepository;
        $this->settingsRepository     = $settingsRepository;
        $this->orgId                  = auth()->user()->org_id;
        $this->database               = $database;
        $this->logger                 = $logger;
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
        try {
            $settings     = $this->transform($all);
            $organisation = $this->transformOrg($all);

            $this->database->beginTransaction();
            $this->settingsRepository->store($settings, $this->orgId);
            $this->organisationRepository->store($organisation, $this->orgId);
            $this->database->commit();

            $this->logger->info(
                'Settings Successfully saved.',
                [
                    'userId'          => auth()->user()->id,
                    'userName'        => auth()->user()->getNameAttribute(),
                    'forOrganisation' => $this->orgId
                ]
            );

            return true;
        } catch (\Exception $exception) {
            $this->database->rollback();

            $this->logger->error(
                sprintf('Error saving Settings due to %s', $exception->getMessage()),
                [
                    'userId'   => auth()->user()->id,
                    'userName' => auth()->user()->getNameAttribute(),
                    'trace'    => $exception->getTraceAsString()
                ]
            );

            return null;
        }
    }

    protected function transform($all)
    {
        $settings = [
                'publishing_type'      => self::DEFAULT_PUBLISHING_TYPE,
                'registry_info'        => [
                    [
                        'publisher_id'  => getVal($all, ['publisherId'], ''),
                        'api_id'        => getVal($all, ['apiKey'], ''),
                        'publish_files' => getVal($all, ['publishFiles'], 'no')
                    ]
                ],
                'default_field_values' => [
                    [
                        'default_currency' => getVal($all, ['defaultCurrency'], ''),
                        'default_language' => getVal($all, ['defaultLanguage'], '')
                    ]
                ],
                'default_field_groups' => $this->defaultFieldGroups,
                'version'              => self::DEFAULT_VERSION,
                'organization_id'      => $this->orgId
        ];

        return $settings;
    }

    protected function transformOrg($all)
    {
        $org['reporting_org'] = [
            [
                'reporting_organization_identifier' => getVal($all, ['organisationIdentifier'], ''),
                "reporting_organization_type"       => getVal($all, ['organisationType'], ''),
                "narrative"                         => [
                    [
                        "narrative" => getVal($all, ['organisationName'], ''),
                        "language"  => getVal($all, ['language'], '')
                    ]
                ]
            ]
        ];

        return $org;
    }
}