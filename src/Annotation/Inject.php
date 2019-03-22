<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumerlabs\Perfumer\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Inject extends Annotation
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    public function onCreate(): void
    {
        parent::onCreate();

        $this->getClassKeeper()->addInjection($this->name, $this->type);
    }
}
