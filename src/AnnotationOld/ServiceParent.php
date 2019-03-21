<?php

namespace Barman\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Barman\AnnotationOld;
use Barman\Service;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceParent extends Service
{
    /**
     * @return string
     */
    public function getCallExpression(): string
    {
        return 'parent::';
    }
}
