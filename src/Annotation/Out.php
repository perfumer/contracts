<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Perfumerlabs\Perfumer\Step\CodeStep;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Out extends CodeStep
{
    /**
     * @var string
     */
    public $name;

    public function onBuild(): void
    {
        parent::onBuild();

        $code = '$_return = $' . $this->name . ';';

        $this->setCode($code);

        $this->getMethodData()->setIsReturning(true);

        $this->addAssertionsToBaseTestData([$this->name]);
    }
}
