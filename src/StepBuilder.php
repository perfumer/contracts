<?php

namespace Perfumer\Component\Contracts;

final class StepBuilder
{
    /**
     * @var \ArrayObject
     */
    private $before_code;

    /**
     * @var \ArrayObject
     */
    private $after_code;

    /**
     * @var \ArrayObject
     */
    private $prepended_code;

    /**
     * @var \ArrayObject
     */
    private $appended_code;

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
     * @var \ArrayObject
     */
    private $arguments;

    /**
     * StepBuilder constructor.
     */
    public function __construct()
    {
        $this->before_code = new \ArrayObject();
        $this->after_code = new \ArrayObject();
        $this->prepended_code = new \ArrayObject();
        $this->appended_code = new \ArrayObject();
        $this->arguments = new \ArrayObject();
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
    public function setValidationCondition(bool $validation_condition)
    {
        $this->validation_condition = $validation_condition;
    }

    /**
     * @return null|string
     */
    public function getExtraCondition()
    {
        return $this->extra_condition;
    }

    /**
     * @param null|string $extra_condition
     */
    public function setExtraCondition($extra_condition)
    {
        $this->extra_condition = $extra_condition;
    }

    /**
     * @return null|string
     */
    public function getReturnExpression()
    {
        return $this->return_expression;
    }

    /**
     * @param null|string $return_expression
     */
    public function setReturnExpression($return_expression)
    {
        $this->return_expression = $return_expression;
    }

    /**
     * @return null|string
     */
    public function getCallExpression()
    {
        return $this->call_expression;
    }

    /**
     * @param null|string $call_expression
     */
    public function setCallExpression($call_expression)
    {
        $this->call_expression = $call_expression;
    }

    /**
     * @return null|string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param null|string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return \ArrayObject
     */
    public function getBeforeCode(): \ArrayObject
    {
        return $this->before_code;
    }

    /**
     * @return \ArrayObject
     */
    public function getAfterCode(): \ArrayObject
    {
        return $this->after_code;
    }

    /**
     * @return \ArrayObject
     */
    public function getPrependedCode(): \ArrayObject
    {
        return $this->prepended_code;
    }

    /**
     * @return \ArrayObject
     */
    public function getAppendedCode(): \ArrayObject
    {
        return $this->appended_code;
    }

    /**
     * @return \ArrayObject
     */
    public function getArguments(): \ArrayObject
    {
        return $this->arguments;
    }
}
