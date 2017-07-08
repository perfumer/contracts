<?php

namespace Perfumer\Contracts\Decorator;

use Perfumer\Contracts\Generator\StepGenerator;

interface StepGeneratorDecorator
{
    public function decorateStepGenerator(StepGenerator $generator): void;
}
