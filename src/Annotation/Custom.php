<?php

namespace Perfumer\Contracts\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumer\Contracts\Annotation;
use Perfumer\Contracts\Generator\StepGenerator;
use Perfumer\Contracts\Step;
use Perfumer\Contracts\Variable\ArgumentVariable;
use Zend\Code\Generator\MethodGenerator as BaseMethodGenerator;
use Zend\Code\Generator\ParameterGenerator;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Custom extends Step
{
    public function decorateGenerators(): void
    {
        parent::decorateGenerators();

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
    }

    /**
     * @return null|StepGenerator|StepGenerator[]
     */
    public function getGenerator()
    {
        $generator = parent::getGenerator();

        $generator->setCallExpression("\$this->");

        return $generator;
    }
}
