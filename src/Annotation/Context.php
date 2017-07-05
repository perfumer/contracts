<?php

namespace Perfumer\Contracts\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationReader;
use Perfumer\Contracts\Annotation;
use Perfumer\Contracts\Exception\DecoratorException;
use Perfumer\Contracts\Generator\StepGenerator;
use Perfumer\Contracts\Step;
use Perfumer\Contracts\Variable\ArgumentVariable;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Context extends Step implements ArgumentVariable
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $class;

    /**
     * @var array
     */
    public $aliases = [];

    /**
     * @throws DecoratorException
     */
    public function decorateGenerators(): void
    {
        if ($this->class !== null) {
            if (!class_exists($this->class) && $this->name !== 'default') {
                throw new DecoratorException(sprintf('"%s" context class not found.',
                    $this->name
                ));
            }

            if (isset($this->getClassGenerator()->getContexts()[$this->name])) {
                throw new DecoratorException(sprintf('"%s" context is already defined.',
                    $this->name
                ));
            }

            if ($this->name !== 'default') {
                $this->getClassGenerator()->addContext($this->name, $this->class);
            }
        }

        // Rest of code is executed when Context is used as Step
        if (!$this->isMethodAnnotation()) {
            return;
        }

        if ($this->name === null) {
            $this->name = 'default';

            $reflection = $this->getReflectionClass();

            $context_class = '\\' . $reflection->getNamespaceName() . '\\' . $reflection->getShortName() . 'Context';

            if (!isset($this->getClassGenerator()->getContexts()[$this->name]) && class_exists($context_class, false)) {
                $this->getClassGenerator()->addContext($this->name, $context_class);
            }
        }

        if (!isset($this->getClassGenerator()->getContexts()[$this->name])) {
            throw new DecoratorException(sprintf('"%s" context is not registered',
                $this->name
            ));
        }

        $annotation_arguments = $this->arguments;

        $reflection_context = new \ReflectionClass($this->getClassGenerator()->getContexts()[$this->name]);

        $method_found = false;

        foreach ($reflection_context->getMethods() as $method) {
            if ($method->getName() !== $this->method) {
                continue;
            }

            $method_found = true;

            $reader = new AnnotationReader();
            $method_annotations = $reader->getMethodAnnotations($method);
            $tmp_arguments = [];

            foreach ($method->getParameters() as $parameter) {
                $found = false;

                foreach ($method_annotations as $method_annotation) {
                    if ($method_annotation instanceof Inject && $parameter->getName() == $method_annotation->name) {
                        /** @var Annotation $variable */
                        $variable = $method_annotation->variable;
                        $variable->setReflectionClass($this->getReflectionClass());
                        $variable->setReflectionMethod($this->getReflectionMethod());
                        $variable->setClassGenerator($this->getClassGenerator());
                        $variable->setMethodGenerator($this->getMethodGenerator());
                        $variable->setTestCaseGenerator($this->getTestCaseGenerator());

                        $tmp_arguments[] = $variable;
                        $found = true;
                    }
                }

                if (!$found) {
                    if ($this->arguments) {
                        if ($annotation_arguments) {
                            $tmp_arguments[] = array_shift($annotation_arguments);
                        }
                    } elseif (!$parameter->isOptional()) {
                        $tmp_arguments[] = $parameter->getName();
                    }
                }
            }

            if (count($annotation_arguments) > 0) {
                throw new DecoratorException(sprintf('%s.%s has excessive arguments.',
                    $this->name,
                    $this->method
                ));
            }

            if ($tmp_arguments) {
                $this->arguments = $tmp_arguments;
            }
        }

        if ($method_found === false) {
            throw new DecoratorException(sprintf('method "%s" is not found in "%s".',
                $this->method,
                $this->name
            ));
        }

        foreach ($this->arguments as $i => $argument) {
            if (is_string($argument) && isset($this->aliases[$argument])) {
                $this->arguments[$i] = $this->aliases[$argument];
            }
        }

        parent::decorateGenerators();
    }

    /**
     * @return null|StepGenerator|StepGenerator[]
     */
    public function getGenerator()
    {
        $generator = parent::getGenerator();

        $name = str_replace('_', '', ucwords($this->name, '_.'));

        $generator->setCallExpression("\$this->get{$name}Context()->");

        return $generator;
    }

    /**
     * @return string
     */
    public function getArgumentVariableName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getArgumentVariableExpression(): string
    {
        return '$this->get' . str_replace('_', '', ucwords($this->name, '_')) . 'Context()';
    }
}
