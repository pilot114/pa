<?php

require './vendor/autoload.php';

$projectDir = '/c/data/httpd/main/www/ml/www/classes';

$finder = new Component\Finder($projectDir);
$fileAnalizer = new Component\FsAnalize($finder);

// в файлах, в которых объявляется класс, ищем строки с "new Exception("
$findedStrings = $fileAnalizer->findBySignature('/^class /', "/new Exception\(/");

dump($findedStrings);