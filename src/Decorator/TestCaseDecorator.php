<?php

namespace Perfumer\Contracts\Decorator;

use Perfumer\Contracts\Generator\TestCaseGenerator;

interface TestCaseDecorator
{
    public function decorateTestCase(TestCaseGenerator $generator): void;
}
