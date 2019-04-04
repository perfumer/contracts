<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Perfumerlabs\Perfumer\Step\PlainStep;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Out extends PlainStep
{
    /**
     * @var string
     */
    public $name;

    public function onCreate(): void
    {
        parent::onCreate();

        $code = '$_return = $' . $this->name . ';';

        $this->getStepData()->setCode($code);
        $this->setIsReturning(true);

        $this->addAssertionsToTestCaseData([$this->name]);
    }
}
