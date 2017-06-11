<?php

namespace Perfumer\Contracts\Decorator;

use Perfumer\Contracts\Generator\MethodGenerator;

interface MethodDecorator
{
    public function decorateMethod(MethodGenerator $generator): void;
}
