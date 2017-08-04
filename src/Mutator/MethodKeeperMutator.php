<?php

namespace Barman\Mutator;

use Barman\Keeper\MethodKeeper;

interface MethodKeeperMutator
{
    public function mutateMethodKeeper(MethodKeeper $keeper): void;
}
