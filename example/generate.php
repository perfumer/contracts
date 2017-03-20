<?php

require __DIR__ . '/../vendor/autoload.php';

$generator = new \Perfumer\Component\Contracts\Generator(new \Perfumer\Component\Contracts\StepParser(), __DIR__ . '/..', [
    'base_src_path' => 'example/generated/src',
    'base_test_path' => 'example/generated/tests',
    'src_path' => 'example/src',
    'test_path' => 'example/tests',
    'contract_prefix' => 'Perfumer\\Component\\Contracts\\Example\\Contract',
    'context_prefix' => 'Perfumer\\Component\\Contracts\\Example',
    'class_prefix' => 'Perfumer\\Component\\Contracts\\Example'
]);

$generator->addTemplateDirectory(__DIR__ . '/src/templates');

$generator->addContext('\\Perfumer\\Component\\Contracts\\Example\\Context\\FooContext');
$generator->generateContexts();

$generator->addClass('\\Perfumer\\Component\\Contracts\\Example\\Contract\\Controller\\FooController');
$generator->generateClasses();