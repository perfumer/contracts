<?php

namespace Barman\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Barman\Annotation;
use Barman\Step;
use Barman\Variable\ArgumentVariable;
use Zend\Code\Generator\MethodGenerator as BaseMethodGenerator;
use Zend\Code\Generator\ParameterGenerator;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Custom extends Step
{
    public function onDecorate(): void
    {
        parent::onDecorate();

        $method = new BaseMethodGenerator();
        $method->setName($this->method);
        $method->setAbstract(true);
        $method->setVisibility('protected');

        foreach ($this->arguments as $item) {
            $name = $item instanceof ArgumentVariable ? $item->getArgumentVariableName() : $item;

            $argument = new ParameterGenerator();
            $argument->setName($name);

            $method->setParameter($argument);
        }

        $this->getClassGenerator()->addMethodFromGenerator($method);

        $this->getStepGenerator()->setCallExpression("\$this->");
    }
}
