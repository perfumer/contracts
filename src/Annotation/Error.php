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
        $this->setValidatingValue(false);

        parent::onCreate();
    }

    public function onBuild(): void
    {
        parent::onBuild();

        $code = '$_return = $' . $this->name . ';';

        $this->setCode($code);

        $this->getMethodData()->setIsReturning(true);

        $this->addAssertionsToBaseTestData([$this->name]);
    }
}
