<?php

namespace Barman\Mutator;

use Barman\Generator\MethodGenerator;

interface MethodGeneratorMutator
{
    public function mutateMethodGenerator(MethodGenerator $generator): void;
}
