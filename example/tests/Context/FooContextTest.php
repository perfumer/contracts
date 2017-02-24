<?php

namespace Tests\Perfumer\Component\Bdd\Example\Context;

class FooContextTest extends \Generated\Tests\Perfumer\Component\Bdd\Example\Context\FooContextTest
{
    /**
     * @return array
     */
    public function intTypeDataProvider()
    {
        return [
            [1, null],
            ['qwerty', 'must be integer'],
        ];
    }

    /**
     * @return array
     */
    public function sumDataProvider()
    {
        return [
            [1, 2, 3],
            [10, 30, 40],
        ];
    }

}
