<?php

namespace Perfumer\Contracts\Decorator;

use Perfumer\Contracts\Generator\MethodGenerator;

interface MethodGeneratorDecorator
{
    public function decorateMethodGenerator(MethodGenerator $generator): void;
}
