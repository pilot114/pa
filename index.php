<?php

require './bootstrap.php';

// $projectDir = '/home/oleg/sources/job/';
$projectDir = '/home/pilot114/sources/job/';

$fileA = new FileAnalize($projectDir . 'scripts/Job');
$serviceA = new ServiceAnalize($projectDir . 'config/container.php');
$classA = new ClassAnalize($projectDir . 'scripts/Job');

// получим информацию по классу
// интерфейсы и трейты чекаются отдельно

$className = 'Job_Toponym_Service';

$classInfo = [
	'inheritanceTree' => $classA->findInheritanceTree($className),
	'whereService' => [],
	'usedService' => [],
	'usedAsClass' => []
];
if (in_array($className, $serviceA->getListClasses())) {
	$classInfo['whereService'] = $serviceA->getWhereService($className);
}
// тут костыль. whereService,usedService - надо искать new в коде

dd($classInfo);
