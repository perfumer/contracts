<?php

namespace Perfumer\Contracts;

use Perfumer\Contracts\Decorator\MethodAnnotationDecorator;
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
    public function getBeforeCode()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getAfterCode()
    {
        return '';
    }

    public function decorateGenerators(): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof Annotation) {
                $step->decorateGenerators();
            }
        }
    }

    /**
     * @param \ReflectionClass $reflection_class
     */
    public function setReflectionClass(\ReflectionClass $reflection_class): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof Annotation) {
                $step->setReflectionClass($reflection_class);
            }
        }

        parent::setReflectionClass($reflection_class);
    }

    /**
     * @param \ReflectionMethod $reflection_method
     */
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
     * @param ClassGenerator $class_generator
     */
    public function setClassGenerator(ClassGenerator $class_generator): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof Annotation) {
                $step->setClassGenerator($class_generator);
            }
        }

        parent::setClassGenerator($class_generator);
    }

    /**
     * @param MethodGenerator $method_generator
     */
    public function setMethodGenerator(MethodGenerator $method_generator): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof Annotation) {
                $step->setMethodGenerator($method_generator);
            }
        }

        parent::setMethodGenerator($method_generator);
    }

    /**
     * @param TestCaseGenerator $test_case_generator
     */
    public function setTestCaseGenerator(TestCaseGenerator $test_case_generator): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof Annotation) {
                $step->setTestCaseGenerator($test_case_generator);
            }
        }

        parent::setTestCaseGenerator($test_case_generator);
    }

    /**
     * @param bool $is_method_annotation
     */
    public function setIsMethodAnnotation(bool $is_method_annotation): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof Annotation) {
                $step->setIsMethodAnnotation($is_method_annotation);
            }
        }

        parent::setIsMethodAnnotation($is_method_annotation);
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
