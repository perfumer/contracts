<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumerlabs\Perfumer\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class AddDefaultContext extends Annotation
{
    public function onCreate(): void
    {
        parent::onCreate();

        $name = $this->getReflectionClass()->getName() . 'Context';

        $this->getClassKeeper()->addContext($name);
    }
}
