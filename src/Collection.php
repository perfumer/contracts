<?php

namespace Perfumer\Contracts;

use Perfumer\Contracts\Decorator\ClassDecorator;
use Perfumer\Contracts\Decorator\MethodAnnotationDecorator;
use Perfumer\Contracts\Decorator\MethodDecorator;
use Perfumer\Contracts\Decorator\TestCaseDecorator;

abstract class Collection extends Step implements MethodAnnotationDecorator
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
            $builders[0]->addBeforeCode('_collection', $this->getBeforeCode());
            $builders[count($builders) - 1]->addAfterCode('_collection', $this->getAfterCode());
        }

        return $builders;
    }

    /**
     * @param ClassBuilder $builder
     */
    public function decorateClass(ClassBuilder $builder): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof ClassDecorator) {
                $step->decorateClass($builder);
            }
        }
    }

    /**
     * @param MethodBuilder $builder
     */
    public function decorateMethod(MethodBuilder $builder): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof MethodDecorator) {
                $step->decorateMethod($builder);
            }
        }
    }

    /**
     * @param TestCaseBuilder $builder
     */
    public function decorateTestCase(TestCaseBuilder $builder): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof TestCaseDecorator) {
                $step->decorateTestCase($builder);
            }
        }
    }

    /**
     * @param Annotation $annotation
     */
    public function decorateMethodAnnotation(Annotation $annotation): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof MethodAnnotationDecorator) {
                $step->decorateMethodAnnotation($annotation);
            }
        }
    }
}
