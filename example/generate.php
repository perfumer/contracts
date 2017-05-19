<?php

require __DIR__ . '/../vendor/autoload.php';

$generator = new \Perfumer\Component\Contracts\Generator(__DIR__ . '/..', [
    'base_src_path' => 'example/generated/src',
    'base_test_path' => 'example/generated/tests',
    'src_path' => 'example/src',
    'test_path' => 'example/tests',
    'contract_prefix' => 'Perfumer\\Component\\Contracts\\Example\\Contract',
    'context_prefix' => 'Perfumer\\Component\\Contracts\\Example',
    'class_prefix' => 'Perfumer\\Component\\Contracts\\Example'
]);

$generator->addAnnotations(__DIR__ . '/src/Annotations.php');

$generator->addClass(\Perfumer\Component\Contracts\Example\Contract\Controller\FooController::class);
$generator->generateClasses();
