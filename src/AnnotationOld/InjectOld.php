<?php

namespace Barman\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Barman\AnnotationOld;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Inject extends AnnotationOld
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
