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
            new \Twig_SimpleFunction('call_arguments', [$this, 'call_arguments']),
            new \Twig_SimpleFunction('return_value', [$this, 'return_value']),
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
            if ($value[0] == '+') {
                $value = substr($value, 1);
            }

            return '$' . $value;
        }, $arguments);

        return $serialize ? implode(', ', $arguments) : $arguments;
    }

    /**
     * @param array $arguments
     * @param bool $serialize
     * @return string|array
     */
    public function call_arguments($arguments, $serialize = true)
    {
        $arguments = array_map(function($value) {
            if ($value[0] == '+') {
                $value = substr($value, 1);

                return '$this->' . $value;
            } else {
                return '$' . $value;
            }
        }, $arguments);

        return $serialize ? implode(', ', $arguments) : $arguments;
    }

    /**
     * @param string $return
     * @return string
     */
    public function return_value($return)
    {
        if (!$return) {
            return '';
        } elseif ($return[0] == '+') {
            $return = substr($return, 1);

            return '$this->' . $return . ' = ';
        } elseif ($return === '>') {
            return '$_return = ';
        } else {
            return '$' . $return . ' = ';
        }
    }
}