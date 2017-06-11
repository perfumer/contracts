<?php

namespace Perfumer\Contracts\Decorator;

use Perfumer\Contracts\Generator\ClassGenerator;

interface ClassGeneratorDecorator
{
    public function decorateClassGenerator(ClassGenerator $generator): void;
}
