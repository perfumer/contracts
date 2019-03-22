<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumerlabs\Perfumer\ClassAnnotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Before extends ClassAnnotation
{
    /**
     * @var array
     */
    public $steps;
}
