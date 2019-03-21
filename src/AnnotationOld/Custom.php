<?php

namespace Barman\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Barman\AnnotationOld;
use Barman\Step;
use Barman\Variable\ArgumentVariable;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Custom extends Step
{
    public function onMutate(): void
    {
        parent::onMutate();

        $method = new MethodGenerator();
        $method->setName($this->method);
        $method->setAbstract(true);
        $method->setVisibility('protected');

        foreach ($this->arguments as $item) {
            $name = $item instanceof ArgumentVariable ? $item->getArgumentVariableName() : $item;

            $argument = new ParameterGenerator();
            $argument->setName($name);

            $method->setParameter($argument);
        }

        $this->getClassKeeper()->getGenerator()->addMethodFromGenerator($method);

        $this->getStepKeeper()->setCallExpression("\$this->");
    }
}
