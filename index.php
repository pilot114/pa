<?php

require './bootstrap.php';

$projectDir = '/home/oleg/sources/job/';
// $projectDir = '/home/pilot114/sources/job/';

$fileA = new FileAnalize($projectDir . 'scripts/Job');
$serviceA = new ServiceAnalize($projectDir . 'config/container.php');
$classA = new ClassAnalize($projectDir . 'scripts/Job');

// только для классов. интерфейсы и трейты чекаются отдельно

$info = new Informer($fileA, $classA, $serviceA);
$info->addClass('Job_Validator_JsonSchema_Format');
// dd($info->build());

$routes = new RouterAnalize($projectDir . 'config/routes');
