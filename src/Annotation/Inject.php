<?php

namespace Perfumer\Contracts\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumer\Contracts\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Inject extends Annotation
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var mixed
     */
    public $variable;
}
