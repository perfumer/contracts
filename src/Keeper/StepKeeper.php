<?php

namespace Barman\Keeper;

final class StepKeeper
{
    /**
     * @var array
     */
    private $external_wrap = [];

    /**
     * @var array
     */
    private $internal_wrap = [];

    /**
     * @var bool
     */
    private $validation_condition = true;

    /**
     * @var null|string
     */
    private $extra_condition;

    /**
     * @var null|string
     */
    private $return_expression;

    /**
     * @var null|string
     */
    private $call_expression;

    /**
     * @var null|string
     */
    private $method;

    /**
     * @var array
     */
    private $arguments = [];

    /**
     * @return array
     */
    public function getExternalWrap(): array
    {
        return $this->external_wrap;
    }

    /**
     * @param array $external_wrap
     */
    public function setExternalWrap(array $external_wrap): void
    {
        $this->external_wrap = $external_wrap;
    }

    /**
     * @param string $key
     * @param string $before_code
     * @param string $after_code
     */
    public function addExternalWrap(string $key, string $before_code, string $after_code): void
    {
        $this->external_wrap[$key] = [$before_code, $after_code];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasExternalWrap(string $key): bool
    {
        return isset($this->external_wrap[$key]);
    }

    /**
     * @return array
     */
    public function getInternalWrap(): array
    {
        return $this->internal_wrap;
    }

    /**
     * @param array $internal_wrap
     */
    public function setInternalWrap(array $internal_wrap): void
    {
        $this->internal_wrap = $internal_wrap;
    }

    /**
     * @param string $key
     * @param string $before_code
     * @param string $after_code
     */
    public function addInternalWrap(string $key, string $before_code, string $after_code): void
    {
        $this->internal_wrap[$key] = [$before_code, $after_code];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasInternalWrap(string $key): bool
    {
        return isset($this->internal_wrap[$key]);
    }

    /**
     * @return bool
     */
    public function isValidationCondition(): bool
    {
        return $this->validation_condition;
    }

    /**
     * @param bool $validation_condition
     */
    public function setValidationCondition(bool $validation_condition): void
    {
        $this->validation_condition = $validation_condition;
    }

    /**
     * @return null|string
     */
    public function getExtraCondition(): ?string
    {
        return $this->extra_condition;
    }

    /**
     * @param null|string $extra_condition
     */
    public function setExtraCondition($extra_condition): void
    {
        $this->extra_condition = $extra_condition;
    }

    /**
     * @return null|string
     */
    public function getReturnExpression(): ?string
    {
        return $this->return_expression;
    }

    /**
     * @param null|string $return_expression
     */
    public function setReturnExpression($return_expression): void
    {
        $this->return_expression = $return_expression;
    }

    /**
     * @return null|string
     */
    public function getCallExpression(): ?string
    {
        return $this->call_expression;
    }

    /**
     * @param null|string $call_expression
     */
    public function setCallExpression($call_expression): void
    {
        $this->call_expression = $call_expression;
    }

    /**
     * @return null|string
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @param null|string $method
     */
    public function setMethod($method): void
    {
        $this->method = $method;
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
     * @param string $argument
     */
    public function addArgument(string $argument): void
    {
        $this->arguments[] = $argument;
    }
}
