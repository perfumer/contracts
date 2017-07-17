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

$generator->addContract(\Perfumer\Contracts\Example\Contract\Example1::class);
$generator->addContract(\Perfumer\Contracts\Example\Contract\Example2::class);
$generator->addContract(\Perfumer\Contracts\Example\Contract\Example3::class);
$generator->addContract(\Perfumer\Contracts\Example\Contract\Example4::class);
$generator->generateAll();
