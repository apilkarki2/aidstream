<?php namespace App\Lite\Services\Profile;

use App\Lite\Contracts\OrganisationRepositoryInterface;
use App\Lite\Contracts\UserRepositoryInterface;

class ProfileService
{
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * @var OrganisationRepositoryInterface
     */
    protected $organisationRepository;

    /**
     * ProfileService constructor.
     * @param UserRepositoryInterface         $userRepository
     * @param OrganisationRepositoryInterface $organisationRepository
     */
    function __construct(UserRepositoryInterface $userRepository, OrganisationRepositoryInterface $organisationRepository)
    {
        $this->userRepository = $userRepository;
        $this->organisationRepository = $organisationRepository;
    }

    public function getOrg($orgId)
    {
        return $this->organisationRepository->find($orgId);
    }

    public function getUser($userId)
    {
        return $this->userRepository->find($userId);
    }
}