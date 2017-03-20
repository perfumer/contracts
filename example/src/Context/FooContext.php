<?php

namespace Perfumer\Component\Contracts\Example\Context;

use Perfumer\Component\Contracts\Annotations\Test;

class FooContext
{
    /**
     * @Test
     *
     * @param $value
     * @return bool
     */
    public function intType($value): bool
    {
        return is_int($value);
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

    /**
     * @param int $a
     * @param int $b
     * @return int
     * @return int
     */
    public function multiply(int $a, int $b)
    {
        return $a * $b;
    }

    /**
     * @Test
     *
     * @param bool $param1_valid
     * @param bool $param2_valid
     * @return string
     */
    public function fooErrors(bool $param1_valid, bool $param2_valid)
    {
        $return = '';

        if (!$param1_valid && $param2_valid) {
            $return = 'Param1 is not valid';
        }

        if ($param1_valid && !$param2_valid) {
            $return = 'Param2 is not valid';
        }

        if (!$param1_valid && !$param2_valid) {
            $return = 'Param1 and param2 are not valid';
        }

        return $return;
    }
}
