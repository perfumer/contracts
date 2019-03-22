<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Perfumerlabs\Perfumer\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class SetFromInject extends Set
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

        $code = '$' . $this->name . ' = $this->get' . str_replace('_', '', ucwords($this->value, '_')) . '();';

        $this->getStepData()->setCode($code);
        $this->getStepData()->setIsValidating(false);
    }
}
