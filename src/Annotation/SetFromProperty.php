<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Perfumerlabs\Perfumer\Annotation;
use Perfumerlabs\Perfumer\Step\PlainStep;
use Zend\Code\Generator\MethodGenerator;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class SetFromProperty extends Set
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

        $code = '$' . $this->name . ' = $this->' . $this->value . ';';

        $this->getStepData()->setCode($code);
        $this->getStepData()->setValidationEnabled(false);
    }
}
