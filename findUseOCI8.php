<?php

require './bootstrap.php';

$projectDir = '/mnt/c/data/httpd/main/www/ml';
$fileAanalize = new FsAnalize(new Finder($projectDir));

$report = [];
foreach (file('data/functions_osi8_extension.txt') as $num => $string) {
	$report[$string] = $fileAanalize->findCountUsage('/' . trim($string) . '\(/');
	echo $num . "\n";
}

dump("Использование в файлах:");
$filesStat = [];
foreach ($report as $files) {
	foreach ($files as $fileName => $count) {
		if (!isset($filesStat[$fileName])) {
			$filesStat[$fileName] = 0;
		}
		$filesStat[$fileName] += $count;
	}
}
dump($filesStat);

dump("Группировка по количеству использований:");
$useStat = [];
foreach ($report as $name => $files) {
	$useStat[$name] = array_sum($files);
}
arsort($useStat);
dump($useStat);