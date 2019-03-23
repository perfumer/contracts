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
        $this->setIsReturning(true);

        parent::onCreate();
    }

    public function onBuild(): void
    {
        parent::onBuild();

        $code = '$_return = $' . $this->name . ';';

        $this->getStepData()->setCode($code);

        $this->addAssertionsToTestCaseData([$this->name]);
    }
}
