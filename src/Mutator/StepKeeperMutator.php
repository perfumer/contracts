<?php

namespace Barman\Mutator;

use Barman\Keeper\StepKeeper;

interface StepKeeperMutator
{
    public function mutateStepKeeper(StepKeeper $keeper): void;
}
