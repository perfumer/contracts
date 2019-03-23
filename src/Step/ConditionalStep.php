<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class ConditionalStep extends CodeStep
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

        if ($this->if) {
            $this->addAssertionsToBaseTestData([$this->if]);
        }

        if ($this->unless) {
            $this->addAssertionsToBaseTestData([$this->unless]);
        }
    }
}
