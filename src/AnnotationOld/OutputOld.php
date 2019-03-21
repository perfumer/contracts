<?php

namespace Barman\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Barman\AnnotationOld;
use Barman\Variable\ReturnedVariable;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Output extends AnnotationOld implements ReturnedVariable
{
    /**
     * @return string
     */
    public function getReturnedVariableExpression(): string
    {
        return '$_return';
    }

    public function onMutate(): void
    {
        $this->getMethodKeeper()->addInitialVariable('_return', 'null');

        if (!isset($this->getMethodKeeper()->getAppendedCode()['_return'])) {
            $this->getMethodKeeper()->addAppendedCode('_return', 'return $_return;');
        }
    }
}