<?php

namespace Barman\Mutator;

use Barman\Annotation;

interface MethodAnnotationMutator
{
    public function mutateMethodAnnotation(Annotation $annotation): void;
}
