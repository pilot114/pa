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
$report->addClass('Job\Domain\Profiles\ProfileService');

dd($report->build());

// trigger -> event -> listen
// $events = new EventAnalize($finder);

// получить ВСЁ, что вызывается в конкретном экшене
// $actions = new ActionAnalize($finder);
// $cli = new CliAnalize($finder);

// нужен для автоматической генерации типовых запросов к апи
// $routes = new RouterAnalize($projectDir . 'config/routes');

// + вводить id хоть чего - получать хоть че с этим id + все связанные сущности
