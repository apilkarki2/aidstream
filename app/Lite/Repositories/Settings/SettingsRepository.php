<?php namespace App\Lite\Repositories\Settings;


use App\Models\Settings;

class SettingsRepository
{

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * SettingsRepository constructor.
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function getSettingsWithOrgId($id)
    {
        return $this->settings->where('organization_id', $id)->first();
    }

    public function store($settings, $orgId)
    {
        return $this->settings->updateorCreate(['organization_id' => $orgId], $settings);
    }
}

