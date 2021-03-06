<?php

namespace Barman;

use Barman\Keeper\StepKeeper;
use Barman\Mutator\MethodAnnotationMutator;
use Barman\Mutator\StepKeeperMutator;
use Barman\Variable\ArgumentVariable;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Collection extends Annotation implements MethodAnnotationMutator, StepKeeperMutator
{
    /**
     * @var array
     */
    public $steps = [];

    /**
     * @var mixed
     */
    public $if;

    /**
     * @var mixed
     */
    public $unless;

    /**
     * @var string
     */
    private $code_key;

    public function onCreate(): void
    {
        $this->code_key = uniqid(null, true);

        parent::onCreate();
    }

    /**
     * @return string
     */
    public function getCodeKey()
    {
        return '_' . $this->code_key;
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
            if ($this->if || $this->unless) {
                $condition = $this->if ?: $this->unless;

                $body_argument = $condition instanceof ArgumentVariable ? $condition->getArgumentVariableExpression() : '$' . $condition;

                if ($this->unless) {
                    $body_argument = '!' . $body_argument;
                }

                $keepers[0]->addBeforeCode($this->getCodeKey() . '_condition', '
                    if (' . $body_argument . ') {
                ');
            }

            $keepers[0]->addBeforeCode($this->getCodeKey(), $this->getBeforeCode());
            $keepers[count($keepers) - 1]->addAfterCode($this->getCodeKey(), $this->getAfterCode());

            if ($this->if || $this->unless) {
                $keepers[count($keepers) - 1]->addAfterCode($this->getCodeKey() . '_condition', '}');
            }
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
