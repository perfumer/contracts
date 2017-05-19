<?php

require __DIR__ . '/../vendor/autoload.php';

$generator = new \Perfumer\Contracts\Generator(__DIR__ . '/..', [
    'base_src_path' => 'example/generated/src',
    'base_test_path' => 'example/generated/tests',
    'src_path' => 'example/src',
    'test_path' => 'example/tests',
    'contract_prefix' => 'Perfumer\\Contracts\\Example\\Contract',
    'context_prefix' => 'Perfumer\\Contracts\\Example',
    'class_prefix' => 'Perfumer\\Contracts\\Example'
]);

$generator->addAnnotations(__DIR__ . '/src/Annotations.php');

$generator->addClass(\Perfumer\Contracts\Example\Contract\Controller\FooController::class);
$generator->generateClasses();
