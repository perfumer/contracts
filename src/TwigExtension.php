<?php

namespace Perfumer\Contracts;

class TwigExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('ucwords', [$this, 'ucwords']),
            new \Twig_SimpleFilter('arguments', [$this, 'arguments']),
        );
    }

    /**
     * @param string $text
     * @return string
     */
    public function ucwords(string $text): string
    {
        return str_replace('_', '', ucwords($text, '_'));
    }

    /**
     * @param MethodBuilder $builder
     * @return string
     */
    public function arguments(MethodBuilder $builder): string
    {
        $array = [];

        foreach ($builder->getArguments() as $argument) {
            if ($argument instanceof Argument) {
                $item = '';

                if ($argument->getType()) {
                    if ($argument->allowsNull()) {
                        $item = '?';
                    }

                    $item .= $argument->getType() . ' ';
                }

                $item .= '$' . $argument->getName();

                if ($argument->getDefaultValue()) {
                    $item .= ' = ' . $argument->getDefaultValue();
                }

                $array[] = $item;
            }

            if ($argument instanceof \ReflectionParameter) {
                $item = '';

                if ($type = $argument->getType()) {
                    if ($argument->allowsNull()) {
                        $item = '?';
                    }

                    $type_name = $type->isBuiltin() ? $argument->getType() : '\\' . $argument->getType();

                    $item .= $type_name . ' ';
                }

                $item .= '$' . $argument->getName();

                if ($argument->isDefaultValueAvailable()) {
                    if (is_array($argument->getDefaultValue())) {
                        $value = '[]';
                    } elseif (is_string($argument->getDefaultValue())) {
                        $value = str_replace("'", "\\'", $argument->getDefaultValue());
                        $value = "'{$value}'";
                    } else {
                        $value = (string) $argument->getDefaultValue();
                    }

                    $item .= ' = ' . $value;
                }

                $array[] = $item;
            }
        }

        return implode(', ', $array);
    }
}
