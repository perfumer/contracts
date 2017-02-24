<?php

require __DIR__ . '/../vendor/autoload.php';

$generator = new \Perfumer\Component\Bdd\Generator(new \Perfumer\Component\Bdd\StepParser(), __DIR__ . '/..', [
    'base_src_path' => 'sandbox/generated/generated/src',
    'base_test_path' => 'sandbox/generated/generated/tests',
    'src_path' => 'sandbox/generated/src',
    'test_path' => 'sandbox/generated/tests',
    'contract_prefix' => 'Perfumer\\Component\\Bdd\\Sandbox\\Contracts',
    'context_prefix' => 'Perfumer\\Component\\Bdd\\Sandbox',
    'class_prefix' => 'Perfumer\\Component\\Bdd\\Sandbox'
]);

$generator->addTemplateDirectory(__DIR__ . '/templates');

$generator->addContext('\\Perfumer\\Component\\Bdd\\Sandbox\\Contexts\\FooContext');
$generator->generateContexts();

$generator->addClass('\\Perfumer\\Component\\Bdd\\Sandbox\\Contracts\\Controller\\FooController');
$generator->generateClasses();
