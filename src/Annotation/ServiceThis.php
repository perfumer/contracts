<?php

namespace Barman\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Barman\Annotation;
use Barman\Service;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceThis extends Service
{
    /**
     * @return string
     */
    public function getCallExpression(): string
    {
        return '$this->';
    }
}
