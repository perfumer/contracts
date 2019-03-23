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

    public function onBuild(): void
    {
        parent::onBuild();

        $this->getClassData()->addInjection($this->name, $this->type);
    }
}
