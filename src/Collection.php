<?php

namespace Barman;

use Barman\Mutator\MethodAnnotationMutator;
use Barman\Mutator\StepGeneratorMutator;
use Barman\Generator\StepGenerator;

abstract class Collection extends Annotation implements MethodAnnotationMutator, StepGeneratorMutator
{
    /**
     * @var array
     */
    public $steps = [];

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
     * @return StepGenerator[]
     */
    public function getStepGenerators(): array
    {
        $generators = [];

        foreach ($this->steps as $step) {
            if ($step instanceof Step) {
                $generators[] = $step->getStepGenerator();
            }

            if ($step instanceof Collection) {
                $generators = array_merge($generators, $step->getStepGenerators());
            }
        }

        if (count($generators) > 0) {
            $generators[0]->addBeforeCode('_collection', $this->getBeforeCode());
            $generators[count($generators) - 1]->addAfterCode('_collection', $this->getAfterCode());
        }

        return $generators;
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
     * @param StepGenerator $generator
     */
    public function mutateStepGenerator(StepGenerator $generator): void
    {
        foreach ($this->steps as $step) {
            if ($step instanceof StepGeneratorMutator) {
                $step->mutateStepGenerator($generator);
            }
        }
    }
}
