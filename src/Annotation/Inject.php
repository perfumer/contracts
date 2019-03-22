<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumerlabs\Perfumer\ClassAnnotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Inject extends ClassAnnotation
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    public function onCreate(): void
    {
        parent::onCreate();

        $this->getClassData()->addInjection($this->name, $this->type);
    }
}
