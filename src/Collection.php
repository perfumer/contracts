<?php

namespace Barman;

use Barman\Keeper\StepKeeper;
use Barman\Mutator\MethodAnnotationMutator;
use Barman\Mutator\StepKeeperMutator;

abstract class Collection extends Annotation implements MethodAnnotationMutator, StepKeeperMutator
{
    /**
     * @var array
     */
    public $steps = [];

    /**
     * @return string
     */
    public function getCodeKey()
    {
        return '_collection';
    }

    /**
     * @return string
     */
    public function getBeforeCode()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getAfterCode()
    {
        return '';
    }

    /**
     * @return StepKeeper[]
     */
    public function getStepKeepers(): array
    {
        $keepers = [];

        foreach ($this->steps as $step) {
            if ($step instanceof Step) {
                $keepers[] = $step->getStepKeeper();
            }

            if ($step instanceof Collection) {
                $keepers = array_merge($keepers, $step->getStepKeepers());
            }
        }

        if (count($keepers) > 0) {
            $keepers[0]->addBeforeCode($this->getCodeKey(), $this->getBeforeCode());
            $keepers[count($keepers) - 1]->addAfterCode($this->getCodeKey(), $this->getAfterCode());
        }

        return $keepers;
    }

    /**
     * @param Annotation $annotation
     */
    public function mutateMethodAnnotation(Annotation $annotation): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof MethodAnnotationMutator) {
                $step->mutateMethodAnnotation($annotation);
            }
        }
    }

    /**
     * @param StepKeeper $keeper
     */
    public function mutateStepKeeper(StepKeeper $keeper): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof StepKeeperMutator) {
                $step->mutateStepKeeper($keeper);
            }
        }
    }
}
