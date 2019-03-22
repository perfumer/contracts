<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumerlabs\Perfumer\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Returns extends Annotation
{
    /**
     * @var array
     */
    public $names;

    /**
     * @var bool
     */
    public $assoc = true;
}
