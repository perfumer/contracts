<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumerlabs\Perfumer\ClassAnnotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class After extends ClassAnnotation
{
    /**
     * @var array
     */
    public $steps;
}
