<?php

namespace Barman\Example;

use Barman\Example\Service\Math;

class Example5Parent
{
    /**
     * @var Math
     */
    protected $math;

    /**
     * Example5Parent constructor.
     * @param Math $math
     */
    public function __construct(Math $math)
    {
        $this->math = $math;
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    public function product($a, $b): int
    {
        return $a * $b;
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    public function quotient($a, $b): int
    {
        return $a / $b;
    }

    /**
     * @param $a
     * @return int
     */
    static public function double($a): int
    {
        return 2 * $a;
    }
}
