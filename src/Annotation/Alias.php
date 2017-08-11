<?php

namespace Barman\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Barman\Annotation;
use Barman\Collection;
use Barman\Mutator\MethodAnnotationMutator;
use Barman\Step;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Alias extends Annotation implements MethodAnnotationMutator
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var mixed
     */
    public $variable;

    public function onCreate(): void
    {
        if ($this->variable instanceof Annotation) {
            $this->variable->setReflectionClass($this->getReflectionClass());
            $this->variable->setReflectionMethod($this->getReflectionMethod());
            $this->variable->setClassKeeper($this->getClassKeeper());
            $this->variable->setTestCaseKeeper($this->getTestCaseKeeper());
            $this->variable->setMethodKeeper($this->getMethodKeeper());
        }
    }

    /**
     * @param Annotation $annotation
     */
    public function mutateMethodAnnotation(Annotation $annotation): void
    {
        if ($annotation instanceof Collection) {
            foreach ($annotation->steps as $step) {
                $this->mutateStep($step);
            }
        } elseif ($annotation instanceof Step) {
            $this->mutateStep($annotation);
        }
    }

    /**
     * @param Step $step
     */
    private function mutateStep(Step $step)
    {
        if ($this->variable instanceof Annotation) {
            $tmp = clone $this->variable;
            $tmp->setStepKeeper($step->getStepKeeper());
        } else {
            $tmp = $this->variable;
        }

        foreach ($step->arguments as $i => $argument) {
            if (is_string($argument) && $argument === $this->name) {
                $step->arguments[$i] = $this->getVariableCopy($tmp);
            }
        }

        if (is_array($step->return)) {
            foreach ($step->return as $i => $return) {
                if (is_string($return) && $return === $this->name) {
                    $step->return[$i] = $this->getVariableCopy($tmp);
                }
            }
        } elseif (is_string($step->return) && $step->return === $this->name) {
            $step->return = $this->getVariableCopy($tmp);
        }

        if (is_string($step->if) && $step->if === $this->name) {
            $step->if = $this->getVariableCopy($tmp);
        }

        if (is_string($step->unless) && $step->unless === $this->name) {
            $step->unless = $this->getVariableCopy($tmp);
        }
    }

    /**
     * @param $variable
     * @return mixed
     */
    private function getVariableCopy($variable)
    {
        return $variable instanceof Annotation ? clone $variable : $variable;
    }
}
