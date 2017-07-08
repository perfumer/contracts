<?php

namespace Perfumer\Contracts\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumer\Contracts\Annotation;
use Perfumer\Contracts\Decorator\MethodAnnotationDecorator;
use Perfumer\Contracts\Step;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Error extends Context implements MethodAnnotationDecorator
{
    public function decorateGenerators(): void
    {
        parent::decorateGenerators();

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
    public function decorateMethodAnnotation(Annotation $annotation): void
    {
        if ($annotation instanceof Step && $annotation->return === $this->unless) {
            $annotation->validate = true;
        }
    }
}
