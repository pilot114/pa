<?php

require './vendor/autoload.php';

$projectDir = './data/src';

$finder = new Component\Finder($projectDir);
$parser = new Component\Parser($finder);

$parser->getListTokens();