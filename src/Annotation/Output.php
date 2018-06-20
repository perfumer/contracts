<?php

namespace Barman\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Barman\Annotation;
use Barman\Variable\ReturnedVariable;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
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

    public function onMutate(): void
    {
        $this->getMethodKeeper()->addInitialVariable('_return', 'null');

        if (!$this->getMethodKeeper()->getReturnCode()) {
            $this->getMethodKeeper()->setReturnCode('return $_return;');
        }
    }
}
