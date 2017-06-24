<?php

require './bootstrap.php';

$projectDir = '/home/pilot114/sources/job/';

echo '<pre>';

$pa = new FileAnalize($projectDir . 'scripts/Job');
$pa->dirStat();
$pa->fileStat();
$pa->view();

$sa = new ServiceAnalize($projectDir . 'config/container.php');
print_r($sa->getInfo());

echo '</pre>';


$ea = new ExtendsAnalize($projectDir . 'scripts/Job');
$ea->buildTree();
$ea->dumpTree();
