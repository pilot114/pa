<?php

require './vendor/autoload.php';

$projectDir = '/c/data/httpd/main/www/ml/www/classes';

$finder = new Component\Finder($projectDir);
$fileAnalizer = new Component\FsAnalize($finder);

$report = [];
echo "Прогресс: \n";
foreach (file('./data/functions_osi8_extension.txt') as $num => $string) {
	$report[trim($string)] = $fileAnalizer->findCountUsage('#' . trim($string) . '\(#');
	echo "*";
}
echo "\n";

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