<?php

namespace Perfumer\Contracts\Decorator;

use Perfumer\Contracts\ClassBuilder;

interface ClassDecorator
{
    public function decorateClass(ClassBuilder $builder): void;
}
