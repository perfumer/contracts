<?php

namespace Perfumer\Component\Contracts;

abstract class Collection extends Step
{
    /**
     * @var array
     */
    public $steps = [];

    /**
     * @return string
     */
    abstract public function getBeforeCode(): string;

    /**
     * @return string
     */
    abstract public function getAfterCode(): string;

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder $method_builder
     * @return null|StepBuilder|StepBuilder[]
     */
    public function getBuilder(ClassBuilder $class_builder, MethodBuilder $method_builder)
    {
        $builders = [];

        foreach ($this->steps as $step) {
            if ($step instanceof Step) {
                $step_builders = $step->getBuilder($class_builder, $method_builder);

                if ($step_builders === null) {
                    continue;
                }

                if (!is_array($step_builders)) {
                    $builders[] = $step_builders;
                } else {
                    $builders = array_merge($builders, $step_builders);
                }
            }
        }

        if (count($builders) > 0) {
            $builders[0]->getBeforeCode()->append($this->getBeforeCode());
            $builders[count($builders) - 1]->getAfterCode()->append($this->getAfterCode());
        }

        return $builders;
    }

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder|null $method_builder
     */
    public function apply(ClassBuilder $class_builder, MethodBuilder $method_builder = null): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof Annotation) {
                $step->apply($class_builder, $method_builder);
            }
        }
    }
}
