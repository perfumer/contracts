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
    private $before_code;

    /**
     * @var string
     */
    private $after_code;

    /**
     * @return string
     */
    public function getBeforeCode(): ?string
    {
        return $this->before_code;
    }

    /**
     * @param string $before_code
     */
    public function setBeforeCode(string $before_code): void
    {
        $this->before_code = $before_code;
    }

    /**
     * @return string
     */
    public function getAfterCode(): ?string
    {
        return $this->after_code;
    }

    /**
     * @param string $after_code
     */
    public function setAfterCode(string $after_code): void
    {
        $this->after_code = $after_code;
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
            /** @var StepKeeper $first_keeper */
            $first_keeper = $keepers[0];

            /** @var StepKeeper $last_keeper */
            $last_keeper = $keepers[count($keepers) - 1];

            if ($this->getBeforeCode()) {
                $first_keeper->setBeforeCode($this->getBeforeCode() . PHP_EOL . PHP_EOL . $first_keeper->getBeforeCode());
            }

            if ($this->if || $this->unless) {
                $condition = $this->if ?: $this->unless;

                $body_argument = $condition instanceof ArgumentVariable ? $condition->getArgumentVariableExpression() : '$' . $condition;

                if ($this->unless) {
                    $body_argument = '!' . $body_argument;
                }

                $first_keeper->setBeforeCode('
                    if (' . $body_argument . ') {
                ' . $first_keeper->getBeforeCode());
            }

            if ($this->getAfterCode()) {
                $last_keeper->setAfterCode($last_keeper->getAfterCode() . PHP_EOL . PHP_EOL . $this->getAfterCode());
            }

            if ($this->if || $this->unless) {
                $last_keeper->setAfterCode($last_keeper->getAfterCode() . PHP_EOL . '}');
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
