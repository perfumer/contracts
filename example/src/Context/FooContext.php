<?php

namespace Perfumer\Component\Contracts\Example\Context;

use Perfumer\Component\Contracts\Annotations\Inject;
use Perfumer\Component\Contracts\Annotations\Property;
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
     * @Inject(name="staff", variable=@Property("staff"))
     * @Test
     *
     * @param int $a
     * @param int $staff
     * @return int
     */
    public function sum(int $a, int $staff)
    {
        return $a + $staff;
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
     * @return string
     */
    public function fooErrors()
    {
        return 'Param1 is not valid';
    }
}
