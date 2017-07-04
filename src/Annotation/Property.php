<?php

namespace Perfumer\Contracts\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumer\Contracts\Annotation;
use Perfumer\Contracts\Variable\ArgumentVariable;
use Perfumer\Contracts\Variable\ReturnedVariable;
use Zend\Code\Generator\PropertyGenerator;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
class Property extends Annotation implements ArgumentVariable, ReturnedVariable
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

    public function decorateGenerators(): void
    {
        if (!$this->getClassGenerator()->hasProperty($this->name)) {
            $this->getClassGenerator()->addProperty($this->name, null, PropertyGenerator::FLAG_PROTECTED);
        }
    }
}
