<?php

require __DIR__ . '/../vendor/autoload.php';

$generator = new \Perfumer\Component\Bdd\Generator(new \Perfumer\Component\Bdd\StepParser(), __DIR__ . '/..', [
    'base_src_path' => 'example/generated/src',
    'base_test_path' => 'example/generated/tests',
    'src_path' => 'example/src',
    'test_path' => 'example/tests',
    'contract_prefix' => 'Perfumer\\Component\\Bdd\\Example\\Contract',
    'context_prefix' => 'Perfumer\\Component\\Bdd\\Example',
    'class_prefix' => 'Perfumer\\Component\\Bdd\\Example'
]);

$generator->addTemplateDirectory(__DIR__ . '/src/templates');

$generator->addContext('\\Perfumer\\Component\\Bdd\\Example\\Context\\FooContext');
$generator->generateContexts();

$generator->addClass('\\Perfumer\\Component\\Bdd\\Example\\Contract\\Controller\\FooController');
$generator->generateClasses();
