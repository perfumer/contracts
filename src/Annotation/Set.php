<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Perfumerlabs\Perfumer\Annotation;
use Perfumerlabs\Perfumer\Step\PlainStep;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Set extends PlainStep
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $value;

    public function onCreate(): void
    {
        parent::onCreate();

        $code = '$' . $this->name . ' = $' . $this->value . ';';

        $this->getStepData()->setCode($code);
        $this->getStepData()->setIsValidating(false);

        $this->addDeclarationsToTestCaseData([$this->name]);
    }
}
