<?php

namespace Barman\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Barman\AnnotationOld;
use Barman\AutoArguments;
use Barman\Exception\MutatorException;
use Barman\Step;
use Barman\Variable\ArgumentVariable;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Injection extends Step implements ArgumentVariable, AutoArguments
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @throws MutatorException
     */
    public function onCreate(): void
    {
        if ($this->isClassAnnotation()) {
            if (!is_string($this->name)) {
                throw new MutatorException('Define name of injection.');
            }

            if ($this->getClassKeeper()->hasInjection($this->name)) {
                throw new MutatorException(sprintf('"%s" injection is already defined.',
                    $this->name
                ));
            }

            $this->getClassKeeper()->addInjection($this->name, $this->type);
        }

        // Rest of code is executed when Injection is used as Step
        if (!$this->isMethodAnnotation()) {
            return;
        }

        if (!isset($this->getClassKeeper()->getInjections()[$this->name])) {
            throw new MutatorException(sprintf('"%s" injection is not registered',
                $this->name
            ));
        }
    }

    public function onMutate(): void
    {
        if ($this->isMethodAnnotation()) {
            parent::onMutate();

            $name = str_replace('_', '', ucwords($this->name, '_.'));

            $this->getStepKeeper()->setCallExpression("\$this->get{$name}()->");
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
        return '$this->_injection_' . $this->name;
    }

    /**
     * @return string
     */
    public function getAutoArgumentsClass(): string
    {
        return $this->getClassKeeper()->getInjections()[$this->name];
    }

    /**
     * @return string
     */
    public function getAutoArgumentsMethod(): string
    {
        return $this->method;
    }
}
