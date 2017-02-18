<?php

namespace Perfumer\Component\Bdd\Sandbox;

use Perfumer\Component\Bdd\Context;

class FooContext implements Context
{
    /**
     * @Test
     *
     * @param $value
     * @return null|string
     */
    public function intType($value)
    {
        return is_int($value) ? null : 'must be integer';
    }

    /**
     * @Test
     *
     * @param int $a
     * @param int $b
     * @return int
     */
    public function sum(int $a, int $b)
    {
        return $a + $b;
    }
}
