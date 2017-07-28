<?php

namespace Barman\Decorator;

use Barman\Annotation;

interface MethodAnnotationDecorator
{
    public function decorateMethodAnnotation(Annotation $annotation): void;
}
