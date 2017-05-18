<?php

namespace Perfumer\Component\Contracts;

interface Annotation
{
    /**
     * @param ClassBuilder $class_builder
     * @param null|MethodBuilder $method_builder
     */
    public function apply(ClassBuilder $class_builder, MethodBuilder $method_builder = null): void;
}
