<?php

namespace Perfumerlabs\Perfumer\Step;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Perfumerlabs\Perfumer\Annotation;
use Perfumerlabs\Perfumer\Data\StepData;

abstract class SharedClassStep extends ExpressionStep
{
    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $method;

    public function onCreate(): void
    {
        $name = str_replace('\\', '', $this->class);

        $this->expression = '$this->get' . $name . 'Context()->' . $this->method;

        parent::onCreate();
    }

    public function getAnnotations()
    {
        $annotations = [];

        $reflection = new \ReflectionClass($this->class);

        foreach ($reflection->getMethods() as $method) {
            if ($method->getName() !== $this->method) {
                continue;
            }

            $reader = new AnnotationReader();
            AnnotationRegistry::registerLoader('class_exists');
            $method_annotations = $reader->getMethodAnnotations($method);

            foreach ($method_annotations as $method_annotation) {
                if ($method_annotation instanceof Annotation) {
                    $method_annotation->setReflectionClass($this->getReflectionClass());
                    $method_annotation->setReflectionMethod($this->getReflectionMethod());
                    $method_annotation->setClassKeeper($this->getClassKeeper());
                    $method_annotation->setTestCaseKeeper($this->getTestCaseKeeper());
                    $method_annotation->setMethodKeeper($this->getMethodKeeper());
                    $method_annotation->setIsMethodAnnotation(true);

                    if ($method_annotation instanceof PlainStep) {
                        $method_annotation->setStepKeeper(new StepData());
                        $method_annotation->setStepData(new StepData());
                    }

                    $method_annotation->onCreate();

                    $annotations[] = $method_annotation;
                }
            }
        }

        return $annotations;
    }
}
