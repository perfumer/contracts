<?php

namespace Perfumer\Contracts\Decorator;

use Perfumer\Contracts\Annotation;

interface MethodAnnotationDecorator
{
    public function decorateMethodAnnotation(Annotation $annotation): void;
}
