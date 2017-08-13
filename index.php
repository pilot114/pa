<?php

require './bootstrap.php';

$projectDir = '/home/oleg/sources/job/';
// $projectDir = '/home/pilot114/sources/job/';

$finder = new Finder($projectDir . 'scripts/Job');

$fileA = new FsAnalize($finder);
$serviceA = new ServiceAnalize($projectDir . 'config/container.php');
$classA = new ClassAnalize($finder);

// только для классов. интерфейсы и трейты чекаются отдельно
$report = new ClassReport($fileA, $classA, $serviceA);
$report->addClass('Job_Model_Entity_Vacancy');
dd($report->build());

// $events = new EventAnalize($finder);

// $events = new ActionAnalize($finder);
// $events = new CliAnalize($finder);

// не особо нужно =)
// $routes = new RouterAnalize($projectDir . 'config/routes');
