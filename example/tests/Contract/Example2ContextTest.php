<?php

namespace Tests\Barman\Example\Contract;

class Example2ContextTest extends \Generated\Tests\Barman\Example\Contract\Example2ContextTest
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
