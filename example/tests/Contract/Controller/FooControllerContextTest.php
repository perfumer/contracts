<?php

namespace Tests\Perfumer\Contracts\Example\Contract\Controller;

class FooControllerContextTest extends \Generated\Tests\Perfumer\Contracts\Example\Contract\Controller\FooControllerContextTest
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
            ['Param1 is not valid'],
        ];
    }
}
