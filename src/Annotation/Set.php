<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Perfumerlabs\Perfumer\Annotation;
use Perfumerlabs\Perfumer\Step\CodeStep;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Set extends CodeStep
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $value;

    /**
     * @var array
     */
    public $tags = [];

    public function onBuild(): void
    {
        parent::onBuild();

        $code = '$' . $this->name . ' = $' . $this->value . ';';

        $this->setCode($code);

        $this->addDeclarationsToBaseTestData([$this->name]);
    }
}
