<?php

namespace Perfumer\Component\Contracts\Example;

class FooService
{
    public function bar($a, $b = 1)
    {
        return 2 * $a;
    }
}
