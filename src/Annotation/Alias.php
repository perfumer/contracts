<?php

namespace Barman\Annotation;

use Barman\Collection;
use Doctrine\Common\Annotations\Annotation\Target;
use Barman\Annotation;
use Barman\Mutator\MethodAnnotationMutator;
use Barman\Step;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
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
        if (!$annotation instanceof Step && !$annotation instanceof Collection) {
            return;
        }

        if ($this->variable instanceof Annotation) {
            $tmp = clone $this->variable;

            if ($annotation instanceof Step) {
                $tmp->setStepKeeper($annotation->getStepKeeper());
            }
        } else {
            $tmp = $this->variable;
        }

        if ($annotation instanceof Step) {
            foreach ($annotation->arguments as $i => $argument) {
                if (is_string($argument) && $argument === $this->name) {
                    $annotation->arguments[$i] = $this->getVariableCopy($tmp);
                }
            }

            if (is_array($annotation->return)) {
                foreach ($annotation->return as $i => $return) {
                    if (is_string($return) && $return === $this->name) {
                        $annotation->return[$i] = $this->getVariableCopy($tmp);
                    }
                }
            } elseif (is_string($annotation->return) && $annotation->return === $this->name) {
                $annotation->return = $this->getVariableCopy($tmp);
            }
        }

        if (is_string($annotation->if) && $annotation->if === $this->name) {
            $annotation->if = $this->getVariableCopy($tmp);
        }

        if (is_string($annotation->unless) && $annotation->unless === $this->name) {
            $annotation->unless = $this->getVariableCopy($tmp);
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
