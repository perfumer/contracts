<?php

namespace Perfumer\Contracts;

use Perfumer\Contracts\Decorator\ClassDecorator;
use Perfumer\Contracts\Decorator\MethodAnnotationDecorator;
use Perfumer\Contracts\Decorator\MethodDecorator;
use Perfumer\Contracts\Decorator\TestCaseDecorator;
use Perfumer\Contracts\Generator\ClassGenerator;
use Perfumer\Contracts\Generator\MethodGenerator;
use Perfumer\Contracts\Generator\StepGenerator;
use Perfumer\Contracts\Generator\TestCaseGenerator;

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

    public function setReflectionClass(\ReflectionClass $reflection_class): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof Annotation) {
                $step->setReflectionClass($reflection_class);
            }
        }

        parent::setReflectionClass($reflection_class);
    }

    public function setReflectionMethod(\ReflectionMethod $reflection_method): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof Annotation) {
                $step->setReflectionMethod($reflection_method);
            }
        }

        parent::setReflectionMethod($reflection_method);
    }

    /**
     * @return null|StepGenerator|StepGenerator[]
     */
    public function getGenerator()
    {
        $generators = [];

        foreach ($this->steps as $step) {
            if ($step instanceof Step) {
                $step_generators = $step->getGenerator();

                if ($step_generators === null) {
                    continue;
                }

                if (!is_array($step_generators)) {
                    $generators[] = $step_generators;
                } else {
                    $generators = array_merge($generators, $step_generators);
                }
            }
        }

        if (count($generators) > 0) {
            $generators[0]->addBeforeCode('_collection', $this->getBeforeCode());
            $generators[count($generators) - 1]->addAfterCode('_collection', $this->getAfterCode());
        }

        return $generators;
    }

    /**
     * @param ClassGenerator $generator
     */
    public function decorateClass(ClassGenerator $generator): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof ClassDecorator) {
                $step->decorateClass($generator);
            }
        }
    }

    /**
     * @param MethodGenerator $generator
     */
    public function decorateMethod(MethodGenerator $generator): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof MethodDecorator) {
                $step->decorateMethod($generator);
            }
        }
    }

    /**
     * @param TestCaseGenerator $generator
     */
    public function decorateTestCase(TestCaseGenerator $generator): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof TestCaseDecorator) {
                $step->decorateTestCase($generator);
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
