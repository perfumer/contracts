<?php

namespace Barman\Mutator;

use Barman\AnnotationOld;

interface MethodAnnotationMutator
{
    public function mutateMethodAnnotation(AnnotationOld $annotation): void;
}
