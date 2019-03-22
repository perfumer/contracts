<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumerlabs\Perfumer\ClassAnnotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class AddDefaultContext extends ClassAnnotation
{
    public function onCreate(): void
    {
        parent::onCreate();

        $name = $this->getReflectionClass()->getName() . 'Context';

        $this->getClassData()->addContext($name);
    }
}
