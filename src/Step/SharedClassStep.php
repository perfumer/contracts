<?php

namespace Perfumerlabs\Perfumer\Step;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Perfumerlabs\Perfumer\Data\StepData;
use Perfumerlabs\Perfumer\MethodAnnotation;

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
            /** @noinspection PhpDeprecationInspection */
            AnnotationRegistry::registerLoader('class_exists');
            $method_annotations = $reader->getMethodAnnotations($method);

            foreach ($method_annotations as $method_annotation) {
                if ($method_annotation instanceof MethodAnnotation) {
                    $method_annotation->setReflectionClass($this->getReflectionClass());
                    $method_annotation->setReflectionMethod($this->getReflectionMethod());
                    $method_annotation->setTestCaseData($this->getTestCaseData());
                    $method_annotation->setMethodData($this->getMethodData());

                    if ($method_annotation instanceof PlainStep) {
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
