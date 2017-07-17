<?php

namespace Tests\Perfumer\Contracts\Example\Context;

class MathTest extends \Generated\Tests\Perfumer\Contracts\Example\Context\MathTest
{
    public function sumDataProvider()
    {
        return [
            [1, 2, 3],
            [0, 5, 5]
        ];
    }

    public function productDataProvider()
    {
        return [
            [1, 2, 2],
            [0, 3, 0]
        ];
    }
}
