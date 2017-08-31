<?php

namespace Barman\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Barman\Annotation;
use Barman\AutoArguments;
use Barman\Exception\MutatorException;
use Barman\Step;
use Barman\Variable\ArgumentVariable;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Context extends Step implements ArgumentVariable, AutoArguments
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

    /**
     * @return string
     */
    public function getAutoArgumentsClass(): string
    {
        return $this->getClassKeeper()->getContexts()[$this->name];
    }

    /**
     * @return string
     */
    public function getAutoArgumentsMethod(): string
    {
        return $this->method;
    }
}
