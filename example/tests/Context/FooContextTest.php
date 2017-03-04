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
            [1, true],
            ['qwerty', false],
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

    public function fooErrorsDataProvider()
    {
        return [
            [true, true, ''],
            [false, true, 'Param1 is not valid'],
            [true, false, 'Param2 is not valid'],
            [false, false, 'Param1 and param2 are not valid'],
        ];
    }
}
