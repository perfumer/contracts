<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumerlabs\Perfumer\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Before extends Annotation
{
    /**
     * @var array
     */
    public $steps;
}
