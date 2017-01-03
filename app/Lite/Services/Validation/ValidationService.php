<?php namespace App\Lite\Services\Validation;

use App\Lite\Services\Validation\Rules\RulesProvider;
use Illuminate\Validation\Factory;

/**
 * Class ValidationService
 * @package App\Lite\Services\Validation
 */
class ValidationService
{
    /**
     * @var
     */
    protected $data;
    /**
     * @var
     */
    protected $entity;
    /**
     * @var
     */
    protected $version;
    /**
     * @var Factory
     */
    protected $factory;
    /**
     * @var
     */
    protected $validator;
    /**
     * @var RulesProvider
     */
    protected $rulesProvider;

    /**
     * ValidationService constructor.
     * @param Factory       $factory
     * @param RulesProvider $rulesProvider
     */
    public function __construct(Factory $factory, RulesProvider $rulesProvider)
    {
        $this->factory       = $factory;
        $this->rulesProvider = $rulesProvider;
    }

    /**
     * @param array $data
     * @param       $entityType
     * @param       $version
     * @return mixed
     */
    public function passes(array $data, $entityType, $version)
    {
        $this->data = $data;

        return $this->{$entityType}($version);
    }

    /**
     * @param string $version
     * @return bool
     */
    public function activity($version = 'V202')
    {
        $this->validator = $this->factory->make(
            $this->data(),
            $this->rulesProvider->getRules($version, 'Activity'),
            $this->rulesProvider->getMessages($version, 'Activity')
        );

        return $this->validator->passes();
    }

    /**
     * @return mixed
     */
    public function errors()
    {
        if ($this->validator) {
            return $this->validator->errors();
        }
    }

    /**
     * @return mixed
     */
    protected function data()
    {
        return $this->data;
    }

}
