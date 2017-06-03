<?php

namespace Perfumer\Contracts\Decorator;

use Perfumer\Contracts\MethodBuilder;

interface MethodDecorator
{
    public function decorateMethod(MethodBuilder $builder): void;
}
