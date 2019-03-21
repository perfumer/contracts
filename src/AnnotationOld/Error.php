<?php

namespace Barman\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Barman\AnnotationOld;
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

        $this->getMethodKeeper()->addInitialVariable('_return', 'null');

        if (!isset($this->getMethodKeeper()->getAppendedCode()['_return'])) {
            $this->getMethodKeeper()->addAppendedCode('_return', 'return $_return;');
        }

        $this->getStepKeeper()->setValidationCondition(false);
        $this->getStepKeeper()->setReturnExpression('$_return');
    }

    /**
     * @param AnnotationOld $annotation
     */
    public function mutateMethodAnnotation(AnnotationOld $annotation): void
    {
        if ($annotation instanceof Step && $annotation->return === $this->unless) {
            $annotation->validate = true;
        }
    }
}
