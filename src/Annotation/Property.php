<?php

namespace Perfumer\Contracts\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumer\Contracts\Annotation;
use Perfumer\Contracts\Decorator\ClassGeneratorDecorator;
use Perfumer\Contracts\Generator\ClassGenerator;
use Perfumer\Contracts\Variable\ArgumentVariable;
use Perfumer\Contracts\Variable\ReturnedVariable;
use Zend\Code\Generator\PropertyGenerator;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Property extends Annotation implements ArgumentVariable, ReturnedVariable, ClassGeneratorDecorator
{
    /**
     * @var string
     */
    public $name;

    /**
     * @param array $values
     */
    public function __construct($values)
    {
        $this->name = $values['value'];
    }

    /**
     * @return string
     */
    public function getArgumentVariableExpression(): string
    {
        return '$this->' . $this->name;
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
    public function getReturnedVariableExpression(): string
    {
        return '$this->' . $this->name;
    }

    /**
     * @param ClassGenerator $generator
     */
    public function decorateClassGenerator(ClassGenerator $generator): void
    {
        if (!$generator->hasProperty($this->name)) {
            $generator->addProperty($this->name, null, PropertyGenerator::FLAG_PROTECTED);
        }
    }
}
