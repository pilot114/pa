<?php

require './vendor/autoload.php';

$projectDir = '/c/data/httpd/main/www/ml/www/classes';

$finder = new Component\Finder($projectDir);
$fileAnalizer = new Component\FsAnalize($finder);

echo $fileAnalizer->stat();
