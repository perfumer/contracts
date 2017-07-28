<?php

namespace Barman\Mutator;

use Barman\Generator\StepGenerator;

interface StepGeneratorMutator
{
    public function mutateStepGenerator(StepGenerator $generator): void;
}
