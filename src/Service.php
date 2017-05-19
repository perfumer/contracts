<?php

namespace Perfumer\Contracts;

abstract class Service extends Step
{
    /**
     * @return string
     */
    abstract public function getCallExpression(): string;

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder $method_builder
     * @return null|StepBuilder|StepBuilder[]
     */
    public function getBuilder(ClassBuilder $class_builder, MethodBuilder $method_builder)
    {
        $step_builder = parent::getBuilder($class_builder, $method_builder);

        $step_builder->setCallExpression($this->getCallExpression());

        return $step_builder;
    }
}
