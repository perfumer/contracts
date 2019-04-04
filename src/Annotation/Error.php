<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Perfumerlabs\Perfumer\Step\ConditionalStep;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Error extends ConditionalStep
{
    /**
     * @var string
     */
    public $name;

    public function onCreate(): void
    {
        parent::onCreate();

        $code = '$_return = $' . $this->name . ';';

        $this->getStepData()->setValidationCondition(false);
        $this->getStepData()->setCode($code);
        $this->setIsReturning(true);

        $this->addAssertionsToTestCaseData([$this->name]);
    }
}
