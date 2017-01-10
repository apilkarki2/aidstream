<?php namespace App\Lite\Services\Profile;

use App\Lite\Contracts\OrganisationRepositoryInterface;
use App\Lite\Contracts\UserRepositoryInterface;
use App\Lite\Services\Data\Traits\TransformsData;
use App\Lite\Services\Traits\ProvidesLoggerContext;
use App\User;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ProfileService
 * @package App\Lite\Services\Profile
 */
class ProfileService
{

    use ProvidesLoggerContext, TransformsData;

    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * @var OrganisationRepositoryInterface
     */
    protected $organisationRepository;

    /**
     * @var DatabaseManager
     */
    protected $database;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ProfileService constructor.
     * @param UserRepositoryInterface         $userRepository
     * @param OrganisationRepositoryInterface $organisationRepository
     * @param DatabaseManager                 $database
     * @param LoggerInterface                 $logger
     */
    function __construct(UserRepositoryInterface $userRepository, OrganisationRepositoryInterface $organisationRepository, DatabaseManager $database, LoggerInterface $logger)
    {
        $this->userRepository         = $userRepository;
        $this->organisationRepository = $organisationRepository;
        $this->database               = $database;
        $this->logger                 = $logger;
    }

    /**
     * Provides Organisation
     *
     * @param $orgId
     * @return \App\Models\Organization\Organization
     */
    public function getOrg($orgId)
    {
        return $this->organisationRepository->find($orgId);
    }

    /**
     * Provides user
     *
     * @param $userId
     * @return mixed
     */
    public function getUser($userId)
    {
        return $this->userRepository->find($userId);
    }

    /**
     * @param       $orgId
     * @param       $userId
     * @param array $rawData
     * @param       $version
     * @return array|null
     */
    public function store($orgId, $userId, array $rawData, $version)
    {
        try {
            if (array_key_exists('picture', $rawData)) {
                $file = $rawData['picture'];

                if (!file_exists(public_path('files/users'))) {
                    mkdir(public_path('files/users'), 0777, true);
                }
                $extension = $file->getClientOriginalExtension();

                $fileName = $userId . '.' . $extension;

                $fileUrl = 'files/users/' . $fileName;

                if ($uploaded = $this->uploadFile($fileName, $file)) {
                    $rawData['fileUrl']  = $fileUrl;
                    $rawData['fileName'] = $fileName;
                }
            }

            $profile = $this->transform($this->getMapping($rawData, 'Profile', $version));

            $this->database->beginTransaction();
            $this->userRepository->update($userId, getVal($profile, ['profile'], []));
            $this->organisationRepository->update($orgId, getVal($profile, ['organisation'], []));
            $this->database->commit();

            $this->logger->info('Settings successfully saved.', $this->getContext());

            return $profile;
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

        return Storage::put('users/' . $fileName, $image);
    }

    /**
     * Provides settings formModel
     *
     * @param $userId
     * @param $orgId
     * @param $version
     * @return array
     */
    public function getFormModel($userId, $orgId, $version)
    {
        $organisation = json_decode($this->organisationRepository->find($orgId), true);
        $user         = json_decode($this->userRepository->find($userId), true);

        $model = array_merge($organisation, $user);

        $filteredModel = $this->transformReverse($this->getMapping($model, 'Profile', $version));

        return $filteredModel;
    }

    /**
     * @param $userId
     * @param $rawData
     * @return bool|null
     */
    public function storePassword($userId, $rawData)
    {
        $currentPassword = User::where('id', $userId)->first();

        if (Hash::check($rawData['oldPassword'], $currentPassword->password)) {
            try {
                $password['password'] = Hash::make(getVal($rawData, ['newPassword'], null));

                $this->database->beginTransaction();
                $this->userRepository->update($userId, $password);
                $this->database->commit();

                $this->logger->info('Password successfully changed.', $this->getContext());

                return true;
            } catch (\Exception $exception) {
                $this->database->rollback();
                $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

                return null;
            }
        }

        return null;
    }
}