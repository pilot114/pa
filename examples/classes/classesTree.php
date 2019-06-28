<?php

require './vendor/autoload.php';

$projectDir = './data/src';

$finder = new Component\Finder($projectDir);
$classAnalizer = new Component\ClassAnalize($finder);

// Получаем дерево наследования Entity
$classAnalizer->buildTree();
$classFamily = $classAnalizer->findInheritanceTree('AbstractRestApi');

dump($classFamily);