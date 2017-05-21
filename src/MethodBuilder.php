<?php

namespace Perfumer\Contracts;

final class MethodBuilder
{
    /**
     * @var bool
     */
    private $is_final = false;

    /**
     * @var bool
     */
    private $is_abstract = false;

    /**
     * @var bool
     */
    private $is_static = false;

    /**
     * @var null|string
     */
    private $access;

    /**
     * @var null|string
     */
    private $name;

    /**
     * @var array
     */
    private $arguments = [];

    /**
     * @var null|string
     */
    private $return_type;

    /**
     * @var array
     */
    private $initial_variables = [];

    /**
     * @var array
     */
    private $test_variables = [];

    /**
     * @var array
     */
    private $prepended_code = [];

    /**
     * @var array
     */
    private $appended_code = [];

    /**
     * @var array
     */
    private $steps = [];

    /**
     * @var bool
     */
    private $validation = false;

    /**
     * @return bool
     */
    public function isFinal(): bool
    {
        return $this->is_final;
    }

    /**
     * @param bool $is_final
     */
    public function setIsFinal(bool $is_final): void
    {
        $this->is_final = $is_final;
    }

    /**
     * @return bool
     */
    public function isAbstract(): bool
    {
        return $this->is_abstract;
    }

    /**
     * @param bool $is_abstract
     */
    public function setIsAbstract(bool $is_abstract): void
    {
        $this->is_abstract = $is_abstract;
    }

    /**
     * @return bool
     */
    public function isStatic(): bool
    {
        return $this->is_static;
    }

    /**
     * @param bool $is_static
     */
    public function setIsStatic(bool $is_static): void
    {
        $this->is_static = $is_static;
    }

    /**
     * @return null|string
     */
    public function getAccess(): ?string
    {
        return $this->access;
    }

    /**
     * @param null|string $access
     */
    public function setAccess($access): void
    {
        $this->access = $access;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    /**
     * @param Argument|\ReflectionParameter $argument
     */
    public function addArgument($argument): void
    {
        $this->arguments[] = $argument;
    }

    /**
     * @return null|string
     */
    public function getReturnType(): ?string
    {
        return $this->return_type;
    }

    /**
     * @param null|string $return_type
     */
    public function setReturnType($return_type): void
    {
        $this->return_type = $return_type;
    }

    /**
     * @return array
     */
    public function getInitialVariables(): array
    {
        return $this->initial_variables;
    }

    /**
     * @param array $initial_variables
     */
    public function setInitialVariables(array $initial_variables): void
    {
        $this->initial_variables = $initial_variables;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function addInitialVariable(string $name, string $value): void
    {
        $this->initial_variables[$name] = $value;
    }

    /**
     * @return array
     */
    public function getTestVariables(): array
    {
        return $this->test_variables;
    }

    /**
     * @param array $test_variables
     */
    public function setTestVariables(array $test_variables): void
    {
        $this->test_variables = $test_variables;
    }

    /**
     * @param string $name
     * @param bool $assert
     */
    public function addTestVariable(string $name, bool $assert): void
    {
        $this->test_variables[] = [$name, $assert];
    }

    /**
     * @return array
     */
    public function getPrependedCode(): array
    {
        return $this->prepended_code;
    }

    /**
     * @param array $prepended_code
     */
    public function setPrependedCode(array $prepended_code): void
    {
        $this->prepended_code = $prepended_code;
    }

    /**
     * @param string $key
     * @param string $code
     */
    public function addPrependedCode(string $key, string $code): void
    {
        $this->prepended_code[$key] = $code;
    }

    /**
     * @return array
     */
    public function getAppendedCode(): array
    {
        return $this->appended_code;
    }

    /**
     * @param array $appended_code
     */
    public function setAppendedCode(array $appended_code): void
    {
        $this->appended_code = $appended_code;
    }

    /**
     * @param string $key
     * @param string $code
     */
    public function addAppendedCode(string $key, string $code): void
    {
        $this->appended_code[$key] = $code;
    }

    /**
     * @return array
     */
    public function getSteps(): array
    {
        return $this->steps;
    }

    /**
     * @param array $steps
     */
    public function setSteps(array $steps): void
    {
        $this->steps = $steps;
    }

    /**
     * @param StepBuilder $step
     */
    public function addStep(StepBuilder $step): void
    {
        $this->steps[] = $step;
    }

    /**
     * @return bool
     */
    public function hasValidation(): bool
    {
        return $this->validation;
    }

    /**
     * @param bool $validation
     */
    public function setValidation(bool $validation): void
    {
        $this->validation = $validation;
    }
}
