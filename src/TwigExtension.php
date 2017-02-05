<?php

namespace Perfumer\Component\Bdd;

class TwigExtension extends \Twig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'perfumer_bdd_extension';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('method_arguments', [$this, 'method_arguments']),
            new \Twig_SimpleFunction('str_replace', [$this, 'str_replace']),
        ];
    }

    /**
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string
     */
    public function str_replace($search, $replace, $subject)
    {
        return str_replace($search, $replace, $subject);
    }

    /**
     * @param array $arguments
     * @param bool $serialize
     * @return string|array
     */
    public function method_arguments($arguments, $serialize = true)
    {
        $arguments = array_map(function($value) {
            if (substr($value, 0, 5) == 'this.') {
                $value = substr($value, 5);
            }

            return '$' . $value;
        }, $arguments);

        return $serialize ? implode(', ', $arguments) : $arguments;
    }
}
