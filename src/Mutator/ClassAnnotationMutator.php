<?php

namespace Barman\Mutator;

use Barman\Annotation;

interface ClassAnnotationMutator
{
    public function mutateClassAnnotation(Annotation $annotation): void;
}
