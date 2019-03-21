<?php

namespace Generated\Annotation\Barman\Example\Contract\Example1Context;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Sum extends \Perfumerlabs\Perfumer\Step\ContextStep
{
    /**
     * @var string
     */
    public $a = 'a';

    /**
     * @var string
     */
    public $b = 'b';

    /**
     * @var string
     */
    public $out = null;

    public function onCreate(): void
    {
        $this->class = 'Barman\\Example\\Contract\\Example1Context';
        $this->method = 'sum';
        $this->arguments = [$this->a, $this->b];
        $this->return = $this->out;

        parent::onCreate();
    }
}
