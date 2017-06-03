<?php

namespace Perfumer\Contracts\Decorator;

use Perfumer\Contracts\Annotation;

interface ClassAnnotationDecorator
{
    public function decorateClassAnnotation(Annotation $annotation): void;
}
