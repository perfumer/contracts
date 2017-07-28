<?php

namespace Barman\Decorator;

use Barman\Generator\StepGenerator;

interface StepGeneratorDecorator
{
    public function decorateStepGenerator(StepGenerator $generator): void;
}
