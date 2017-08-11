<?php

require __DIR__ . '/../vendor/autoload.php';

$generator = new \Barman\Generator(__DIR__ . '/..', [
    'base_src_path' => 'example/generated/src',
    'base_test_path' => 'example/generated/tests',
    'src_path' => 'example/src',
    'test_path' => 'example/tests',
    'contract_prefix' => 'Barman\\Example\\Contract',
    'context_prefix' => 'Barman\\Example',
    'class_prefix' => 'Barman\\Example'
]);

$generator->addContract(\Barman\Example\Contract\Example1::class);
$generator->addContract(\Barman\Example\Contract\Example2::class);
$generator->addContract(\Barman\Example\Contract\Example3::class);
$generator->addContract(\Barman\Example\Contract\Example4::class);
$generator->addContract(\Barman\Example\Contract\Example5::class);
$generator->addContract(\Barman\Example\Contract\Example6::class);
$generator->generateAll();
