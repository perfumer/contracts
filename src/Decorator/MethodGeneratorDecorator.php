<?php

namespace Barman\Decorator;

use Barman\Generator\MethodGenerator;

interface MethodGeneratorDecorator
{
    public function decorateMethodGenerator(MethodGenerator $generator): void;
}
