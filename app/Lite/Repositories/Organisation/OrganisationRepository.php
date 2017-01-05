<?php namespace App\Lite\Repositories\Organisation;

use App\Models\Organization\Organization;

class OrganisationRepository
{
    /**
     * @var Organization
     */
    private $organisation;

    public function __construct(Organization $organisation)
    {
        $this->organisation = $organisation;
    }

    public function getOrg($id)
    {
        return $this->organisation->where('id', $id)->first();
    }

    public function store($organisation, $orgId)
    {
        return $this->organisation->updateorCreate(['id' => $orgId], $organisation);
    }
}

