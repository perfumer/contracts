<?php

namespace Perfumer\Component\Contracts\Example\Controller;

class FooController extends \Generated\Perfumer\Component\Contracts\Example\Controller\FooController
{
    protected function sumDoubled($sum)
    {
        return 2 * $sum;
    }

    public function skipped()
    {
    }
}
