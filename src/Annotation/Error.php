<?php

namespace Barman\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Barman\Annotation;
use Barman\Mutator\MethodAnnotationMutator;
use Barman\Step;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Error extends Context implements MethodAnnotationMutator
{
    public function onMutate(): void
    {
        parent::onMutate();

        $this->getMethodGenerator()->addInitialVariable('_return', 'null');

        if (!isset($this->getMethodGenerator()->getAppendedCode()['_return'])) {
            $this->getMethodGenerator()->addAppendedCode('_return', 'return $_return;');
        }

        $this->getStepGenerator()->setValidationCondition(false);
        $this->getStepGenerator()->setReturnExpression('$_return');
    }

    /**
     * @param Annotation $annotation
     */
    public function mutateMethodAnnotation(Annotation $annotation): void
    {
        if ($annotation instanceof Step && $annotation->return === $this->unless) {
            $annotation->validate = true;
        }
    }
}
