<?php

namespace Perfumer\Component\Contracts\Example;

class ParentController
{
    public function sandboxActionTwo($param1, $param2)
    {
        return [$param1, $param2];
    }
}