<?php

namespace Barman\Decorator;

use Barman\Annotation;

interface ClassAnnotationDecorator
{
    public function decorateClassAnnotation(Annotation $annotation): void;
}
