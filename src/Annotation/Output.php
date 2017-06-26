<?php

namespace Perfumer\Contracts\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumer\Contracts\Annotation;
use Perfumer\Contracts\Decorator\MethodGeneratorDecorator;
use Perfumer\Contracts\Generator\MethodGenerator;
use Perfumer\Contracts\Variable\ReturnedVariable;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Output extends Annotation implements ReturnedVariable, MethodGeneratorDecorator
{
    /**
     * @return string
     */
    public function getReturnedVariableExpression(): string
    {
        return '$_return';
    }

    /**
     * @param MethodGenerator $generator
     */
    public function decorateMethodGenerator(MethodGenerator $generator): void
    {
        $generator->addInitialVariable('_return', 'null');

        if (!isset($generator->getAppendedCode()['_return'])) {
            $generator->addAppendedCode('_return', 'return $_return;');
        }
    }
}
