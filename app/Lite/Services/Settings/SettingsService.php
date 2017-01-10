<?php namespace App\Lite\Services\Settings;

use App\Lite\Contracts\SettingsRepositoryInterface;
use App\Lite\Contracts\OrganisationRepositoryInterface;
use App\Lite\Repositories\Settings\SettingsRepository;
use App\Lite\Services\Data\Traits\TransformsData;
use App\Lite\Services\Traits\ProvidesLoggerContext;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class SettingsService
 * @package App\Lite\Services\Settings
 */
class SettingsService
{

    use ProvidesLoggerContext, TransformsData;

    /**
     * @var OrganisationRepositoryInterface
     */
    protected $organisationRepository;

    /**
     * @var SettingsRepository
     */
    protected $settingsRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DatabaseManager
     */
    protected $database;

    /**
     * SettingsService constructor.
     * @param OrganisationRepositoryInterface $organisationRepository
     * @param SettingsRepositoryInterface     $settingsRepository
     * @param DatabaseManager                 $database
     * @param LoggerInterface                 $logger
     */
    public function __construct(
        OrganisationRepositoryInterface $organisationRepository,
        SettingsRepositoryInterface $settingsRepository,
        DatabaseManager $database,
        LoggerInterface $logger
    ) {
        $this->organisationRepository = $organisationRepository;
        $this->settingsRepository     = $settingsRepository;
        $this->database               = $database;
        $this->logger                 = $logger;
    }

    /**
     * Provides settings formModel
     *
     * @param $orgId
     * @param $version
     * @return array
     */
    public function getSettingsModel($orgId, $version)
    {
        $organisation = json_decode($this->organisationRepository->find($orgId), true);
        $settings     = json_decode($this->settingsRepository->getSettingsWithOrgId($orgId), true);

        $model = array_merge($organisation, $settings);

        $filteredModel = $this->transformReverse($this->getMapping($model, 'Settings', $version));

        return $filteredModel;
    }

    /**
     * Stores settings data
     *
     * @param $orgId
     * @param $rawData
     * @param $version
     * @return array|null
     */
    public function store($orgId, array $rawData, $version)
    {
        try {

            if (array_key_exists('picture', $rawData)) {
                $file = $rawData['picture'];

                if (!file_exists(public_path('files/logos'))) {
                    mkdir(public_path('files/logos'));
                }
                $extension = $file->getClientOriginalExtension();

                $fileName = $orgId . '.' . $extension;

                $fileUrl = 'files/logos/' . $fileName;

                if ($uploaded = $this->uploadFile($fileName, $file)) {
                    $rawData['fileUrl']  = $fileUrl;
                    $rawData['fileName'] = $fileName;
                }
            }

            $settings = $this->transform($this->getMapping($rawData, 'Settings', $version));

            $this->database->beginTransaction();
            $this->settingsRepository->saveWithOrgId($orgId, getVal($settings, ['settings'], []));
            $this->organisationRepository->update($orgId, getVal($settings, ['organisation'], []));
            $this->database->commit();

            $this->logger->info('Settings successfully saved.', $this->getContext());

            return $settings;
        } catch (\Exception $exception) {
            $this->database->rollback();
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }

    /**
     * Uploads file
     *
     * @param              $fileName
     * @param UploadedFile $file
     * @return mixed
     */
    protected
    function uploadFile(
        $fileName,
        UploadedFile $file
    ) {
        $image = Image::make(File::get($file))->fit(
            166,
            166,
            function ($constraint) {
                $constraint->aspectRatio();
            }
        )->encode();

        return Storage::put('logos/' . $fileName, $image);
    }
}