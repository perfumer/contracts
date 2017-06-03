<?php

namespace Perfumer\Contracts\Decorator;

use Perfumer\Contracts\TestCaseBuilder;

interface TestCaseDecorator
{
    public function decorateTestCase(TestCaseBuilder $builder): void;
}
