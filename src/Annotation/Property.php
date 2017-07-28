<?php

namespace Barman\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Barman\Annotation;
use Barman\Variable\ArgumentVariable;
use Barman\Variable\ReturnedVariable;
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

    public function onMutate(): void
    {
        if (!$this->getClassGenerator()->hasProperty($this->name)) {
            $this->getClassGenerator()->addProperty($this->name, null, PropertyGenerator::FLAG_PROTECTED);
        }
    }
}
