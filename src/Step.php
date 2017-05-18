<?php

namespace Perfumer\Component\Contracts;

abstract class Step implements Annotation
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $method;

    /**
     * @var array
     */
    public $arguments = [];

    /**
     * @var mixed
     */
    public $return;

    /**
     * @var string
     */
    public $if;

    /**
     * @var string
     */
    public $unless;

    /**
     * @var bool
     */
    public $validate = false;

    /**
     * @return string
     */
    public function getPrependedCode()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getAppendedCode()
    {
        return '';
    }

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder $method_builder
     * @return null|StepBuilder|StepBuilder[]
     * @throws ContractsException
     */
    public function getBuilder(ClassBuilder $class_builder, MethodBuilder $method_builder)
    {
        $step_builder = new StepBuilder();
        $step_builder->setMethod($this->method);
        $step_builder->getPrependedCode()->append($this->getPrependedCode());
        $step_builder->getAppendedCode()->append($this->getAppendedCode());
        $step_builder->setValidationCondition(true);

        if ($this->if || $this->unless) {
            $condition = $this->if ?: $this->unless;

            $body_argument = $condition instanceof Variable ? $condition->asArgument() : '$' . $condition;

            if ($this->unless) {
                $body_argument = '!' . $body_argument;
            }

            $step_builder->setExtraCondition($body_argument);

            if (is_string($condition)) {
                if (!$method_builder->getInitialVariables()->offsetExists($condition)) {
                    $method_builder->getInitialVariables()->offsetSet($condition, null);
                }
            }
        }

        foreach ($this->arguments as $argument) {
            if (is_string($argument)) {
                $method_builder->getTestVariables()->offsetSet($argument, true);
            }

            $value = $argument instanceof Variable ? $argument->asArgument() : '$' . $argument;

            $step_builder->getArguments()->append($value);
        }

        if ($this->return) {
            if (is_array($this->return)) {
                foreach ($this->return as $value) {
                    if (is_string($value)) {
                        $method_builder->getTestVariables()->offsetSet($value, false);
                    }
                }

                $vars = array_map(function ($v) {
                    return $v instanceof Variable ? $v->asReturn() : '$' . $v;
                }, $this->return);

                $expression = 'list(' . implode(', ', $vars) . ')';
            } else {
                if (is_string($this->return)) {
                    $method_builder->getTestVariables()->offsetSet($this->return, false);
                }

                $expression = $this->return instanceof Variable ? $this->return->asReturn() : '$' . $this->return;
            }

            $step_builder->setReturnExpression($expression);

            $return_values = is_array($this->return) ? $this->return : [$this->return];

            foreach ($return_values as $var) {
                if (!$var instanceof Variable) {
                    $value = $this->validate ? 'true' : 'null';

                    if ($method_builder->getInitialVariables()->offsetExists($var)) {
                        throw new ContractsException(sprintf('%s\\%s -> %s -> %s.%s returns "%s" which is already in use.',
                            $class_builder->getNamespace(),
                            $class_builder->getClassName(),
                            $method_builder->getName(),
                            $this->name,
                            $this->method,
                            $var
                        ));
                    }

                    $method_builder->getInitialVariables()->offsetSet($var, $value);
                }
            }
        }

        if ($this->validate) {
            $method_builder->setValidation(true);

            $step_builder->setReturnExpression('$_valid = (bool) ' . $step_builder->getReturnExpression());
        }

        return $step_builder;
    }

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder|null $method_builder
     */
    public function apply(ClassBuilder $class_builder, MethodBuilder $method_builder = null): void
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof Annotation) {
                $argument->apply($class_builder, $method_builder);
            }
        }

        if (is_array($this->return)) {
            foreach ($this->return as $return) {
                if ($return instanceof Annotation) {
                    $return->apply($class_builder, $method_builder);
                }
            }
        } elseif ($this->return instanceof Annotation) {
            $this->return->apply($class_builder, $method_builder);
        }

        if ($this->if instanceof Annotation) {
            $this->if->apply($class_builder, $method_builder);
        }

        if ($this->unless instanceof Annotation) {
            $this->unless->apply($class_builder, $method_builder);
        }
    }
}
