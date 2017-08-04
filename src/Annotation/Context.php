<?php

namespace Barman\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationReader;
use Barman\Annotation;
use Barman\Exception\MutatorException;
use Barman\Step;
use Barman\Variable\ArgumentVariable;

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
     * @throws MutatorException
     */
    public function onCreate(): void
    {
        if ($this->isClassAnnotation()) {
            if (!is_string($this->name)) {
                throw new MutatorException('Define name of context.');
            }

            if ($this->name === 'default') {
                throw new MutatorException('"Default" name of context is reserved.');
            }

            if ($this->getClassKeeper()->hasContext($this->name)) {
                throw new MutatorException(sprintf('"%s" context is already defined.',
                    $this->name
                ));
            }

            if (!class_exists($this->class)) {
                throw new MutatorException(sprintf('"%s" context class not found.',
                    $this->name
                ));
            }

            $this->getClassKeeper()->addContext($this->name, $this->class);
        }

        // Rest of code is executed when Context is used as Step
        if (!$this->isMethodAnnotation()) {
            return;
        }

        if ($this->name === null) {
            $this->name = 'default';

            $reflection = $this->getReflectionClass();

            $context_class = '\\' . $reflection->getNamespaceName() . '\\' . $reflection->getShortName() . 'Context';

            if (!isset($this->getClassKeeper()->getContexts()[$this->name]) && class_exists($context_class, false)) {
                $this->getClassKeeper()->addContext($this->name, $context_class);
            }
        }

        if (!isset($this->getClassKeeper()->getContexts()[$this->name])) {
            throw new MutatorException(sprintf('"%s" context is not registered',
                $this->name
            ));
        }

        $annotation_arguments = $this->arguments;

        $reflection_context = new \ReflectionClass($this->getClassKeeper()->getContexts()[$this->name]);

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
                        $variable->setClassKeeper($this->getClassKeeper());
                        $variable->setMethodKeeper($this->getMethodKeeper());
                        $variable->setTestCaseKeeper($this->getTestCaseKeeper());
                        $variable->setStepKeeper($this->getStepKeeper());

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
                throw new MutatorException(sprintf('%s.%s has excessive arguments.',
                    $this->name,
                    $this->method
                ));
            }

            if ($tmp_arguments) {
                $this->arguments = $tmp_arguments;
            }
        }

        if ($method_found === false) {
            throw new MutatorException(sprintf('method "%s" is not found in "%s".',
                $this->method,
                $this->name
            ));
        }
    }

    public function onMutate(): void
    {
        if ($this->isMethodAnnotation()) {
            parent::onMutate();

            $name = str_replace('_', '', ucwords($this->name, '_.'));

            $this->getStepKeeper()->setCallExpression("\$this->get{$name}Context()->");
        }
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
