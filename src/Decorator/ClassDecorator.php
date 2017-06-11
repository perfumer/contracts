<?php

namespace Perfumer\Contracts\Decorator;

use Perfumer\Contracts\Generator\ClassGenerator;

interface ClassDecorator
{
    public function decorateClass(ClassGenerator $generator): void;
}
