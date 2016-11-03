<?php namespace App\Services\XmlImporter\Mapper;


/**
 * Class Template
 * @package App\Services\XmlImporter\Mapper
 */
class Template
{
    /**
     * Relative path for the template files.
     *
     * @var string
     */
    protected $relativeTemplatePath = '/Services/XmlImporter/Templates';

    /**
     * Template for a specific Xml version.
     *
     * @var null
     */
    protected $template = null;

    /**
     * Get the template for a specific Xml version.
     *
     * @return null
     */
    public function get($key = null)
    {
        if (!$key) {
            return $this->template;
        }

        return $this->template[$key];
    }

    /**
     * Load template for a specific version.
     *
     * @param string $version
     * @return array
     */
    public function loadFor($version = '2.02')
    {
        $this->template = json_decode($this->read($version), true);
    }

    /**
     * Read the template file.
     *
     * @param $version
     * @return string
     */
    protected function read($version)
    {
        return file_get_contents(sprintf('%s/%s.json', $this->templatePath(), $this->clean($version)));
    }

    /**
     * Remove unwanted '.' character from the IATI version.
     *
     * @param $version
     * @return string
     */
    protected function clean($version)
    {
        return 'V' . str_replace('.', '', $version);
    }

    /**
     * @return string
     */
    protected function templatePath()
    {
        return app_path() . $this->relativeTemplatePath();
    }

    /**
     * Get the relative path for the template files.
     * @return string
     */
    protected function relativeTemplatePath()
    {
        return $this->relativeTemplatePath;
    }
}
