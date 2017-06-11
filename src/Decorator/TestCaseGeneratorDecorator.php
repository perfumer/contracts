<?php

namespace Perfumer\Contracts\Decorator;

use Perfumer\Contracts\Generator\TestCaseGenerator;

interface TestCaseGeneratorDecorator
{
    public function decorateTestCaseGenerator(TestCaseGenerator $generator): void;
}
