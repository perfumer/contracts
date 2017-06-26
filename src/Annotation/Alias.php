<?php

namespace Perfumer\Contracts\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumer\Contracts\Annotation;
use Perfumer\Contracts\Collection;
use Perfumer\Contracts\Decorator\MethodAnnotationDecorator;
use Perfumer\Contracts\Step;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Alias extends Annotation implements MethodAnnotationDecorator
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var mixed
     */
    public $variable;

    /**
     * @param Annotation $annotation
     */
    public function decorateMethodAnnotation(Annotation $annotation): void
    {
        if ($annotation instanceof Collection) {
            foreach ($annotation->steps as $step) {
                $this->decorateStep($step);
            }
        } elseif ($annotation instanceof Step) {
            $this->decorateStep($annotation);
        }
    }

    /**
     * @param Step $step
     */
    private function decorateStep(Step $step)
    {
        if ($step instanceof Context || $step instanceof Injection) {
            $step->aliases[$this->name] = $this->variable;
        }

        foreach ($step->arguments as $i => $argument) {
            if (is_string($argument) && $argument === $this->name) {
                $step->arguments[$i] = $this->variable;
            }
        }

        if (is_array($step->return)) {
            foreach ($step->return as $i => $return) {
                if (is_string($return) && $return === $this->name) {
                    $step->return[$i] = $this->variable;
                }
            }
        } elseif (is_string($step->return) && $step->return === $this->name) {
            $step->return = $this->variable;
        }

        if (is_string($step->if) && $step->if === $this->name) {
            $step->if = $this->variable;
        }

        if (is_string($step->unless) && $step->unless === $this->name) {
            $step->unless = $this->variable;
        }
    }
}
