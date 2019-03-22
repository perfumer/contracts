<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumerlabs\Perfumer\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class After extends Annotation
{
    /**
     * @var array
     */
    public $steps;
}
