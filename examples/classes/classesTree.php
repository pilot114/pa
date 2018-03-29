<?php

require './vendor/autoload.php';

$projectDir = '/c/data/httpd/main/www/ml/www/classes';

$finder = new Component\Finder($projectDir);
$classAnalizer = new Component\ClassAnalize($finder);

// Получаем дерево наследования Entity
$classAnalizer->buildTree();
$classFamily = $classAnalizer->findInheritanceTree('Entity');

dump($classFamily);