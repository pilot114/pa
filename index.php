<?php

require './bootstrap.php';

$pa = new FileAnalize('/home/oleg/sources/job/scripts/Job');
// $pa->dirStat();
$pa->fileStat();
$pa->view();

$sa = new ServiceAnalize('/home/oleg/sources/job/config/container.php');
print_r($sa->getInfo());
