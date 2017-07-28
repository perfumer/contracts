<?php

namespace Barman\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Barman\Annotation;

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
