<?php

require './vendor/autoload.php';

$projectDir = './data/src';

$finder = new Component\Finder($projectDir);
$fileAnalizer = new Component\FsAnalize($finder);

echo $fileAnalizer->stat();
