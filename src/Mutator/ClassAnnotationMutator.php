<?php

namespace Barman\Mutator;

use Barman\AnnotationOld;

interface ClassAnnotationMutator
{
    public function mutateClassAnnotation(AnnotationOld $annotation): void;
}
