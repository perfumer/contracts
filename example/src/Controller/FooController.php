<?php

namespace Perfumer\Contracts\Example\Controller;

class FooController extends \Generated\Perfumer\Contracts\Example\Controller\FooController
{
    protected function sumDoubled($sum)
    {
        return 2 * $sum;
    }

    public function skipped()
    {
    }
}
