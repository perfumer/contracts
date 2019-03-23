<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class ConditionalStep extends PlainStep
{
    /**
     * @var string
     */
    public $if;

    /**
     * @var string
     */
    public $unless;

    public function onBuild(): void
    {
        parent::onBuild();

        $step_data = $this->getStepData();
        $step_data->setValidationCondition(true);

        if ($this->if || $this->unless) {
            $condition = $this->if ?: $this->unless;

            $body_argument = '$' . $condition;

            if ($this->unless) {
                $body_argument = '!' . $body_argument;
            }

            $step_data->setExtraCondition($body_argument);
        }

        if ($this->if) {
            $this->addAssertionsToTestCaseData([$this->if]);
        }

        if ($this->unless) {
            $this->addAssertionsToTestCaseData([$this->unless]);
        }
    }
}
