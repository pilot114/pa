<?php

require './vendor/autoload.php';

$projectDir = './data/src';

$finder = new Component\Finder($projectDir);
$fileAnalizer = new Component\FsAnalize($finder);

// в файлах, в которых объявляется класс, ищем строки с "new Exception("
$findedStrings = $fileAnalizer->findBySignature('/^class /', "/new Exception\(/");

dump($findedStrings);