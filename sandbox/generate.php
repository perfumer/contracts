<?php

require __DIR__ . '/../vendor/autoload.php';

$generator = new \Perfumer\Component\Bdd\Generator(new \Perfumer\Component\Bdd\StepParser(), __DIR__ . '/..', [
    'base_src_path' => 'sandbox/generated/generated/src',
    'base_test_path' => 'sandbox/generated/generated/tests',
    'src_path' => 'sandbox/generated/src',
    'test_path' => 'sandbox/generated/tests',
]);

$generator->addContext(new \Perfumer\Component\Bdd\Sandbox\Context());
$generator->generate();
