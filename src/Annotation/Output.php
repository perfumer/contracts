<?php

namespace Perfumer\Contracts\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumer\Contracts\Annotation;
use Perfumer\Contracts\Variable\ReturnedVariable;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
class Output extends Annotation implements ReturnedVariable
{
    /**
     * @return string
     */
    public function getReturnedVariableExpression(): string
    {
        return '$_return';
    }

    public function decorateGenerators(): void
    {
        $this->getMethodGenerator()->addInitialVariable('_return', 'null');

        if (!isset($this->getMethodGenerator()->getAppendedCode()['_return'])) {
            $this->getMethodGenerator()->addAppendedCode('_return', 'return $_return;');
        }
    }
}
