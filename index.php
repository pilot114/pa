<?php
include './vendor/autoload.php';

use Symfony\Component\Finder\Finder;

if ( !function_exists('array_column') ) {
	function array_column ($input, $columnKey, $indexKey = null) {
	    if (!is_array($input)) {
	        return false;
	    }
	    if ($indexKey === null) {
	        foreach ($input as $i => &$in) {
	            if (is_array($in) && isset($in[$columnKey])) {
	                $in    = $in[$columnKey];
	            } else {
	                unset($input[$i]);
	            }
	        }
	    } else {
	        $result = [];
	        foreach ($input as $i => $in) {
	            if (is_array($in) && isset($in[$columnKey])) {
	                if (isset($in[$indexKey])) {
	                    $result[$in[$indexKey]] = $in[$columnKey];
	                } else {
	                    $result[] = $in[$columnKey];
	                }
	                unset($input[$i]);
	            }
	        }
	        $input = &$result;
	    }
	    return $input;
	}
}


/*

Project Helper - утилита для анализа и обслуживания кода проекта.

Analize - статический анализ структуры проекта и php-кода
(часть проекта для активного анализа указывается в конфиге)

https://github.com/facebook/pfff
https://github.com/squizlabs/PHP_CodeSniffer
https://github.com/sebastianbergmann/phpcpd
https://github.com/phpmd/phpmd
+ SensioLabsInsight

API helper - консольный интерфейс для API проекта
Cli helper - запуск cli
Page helper - запуск экшенов

run:api
run:cli
run:page

Git Helper - всякое типа git tag -l 'build*' | wc -l
полезен для получения инфы о актуальности кода

Добавить Подозреваку -
"Подозреваю, что этот класс/метод нигде не используется!"
"Подозреваю, что это копипаста!"
"Подозреваю, что это эти методы можно вынести в трейт!"
про непонятно что означающие цифры и названия переменных:
"Подозреваю, что это волшебство!"
Класс явно не похож на другие классы рядом (если их много), наследуется не от того
"Подозреваю, что это не должно быть здесь!"

Добавить Шерлока -
ищет всё, что взаимодействет с сущностью, включая
иерархию классов и цепочки вызовов
для чего? утилита должна по умному находить весь код, который может выполниться перед
определенной строкой (функция над деревом зависимостей)

прикрутить tinker - для ручного анализа классов проекта


*/

class ProjectAnalize
{
	private $path;
	private $output;
	// https://symfony.com/doc/current/components/finder.html

	// in
	// searchInDirectory
	// directories
	// files
	// filter
	// depth
	// date
	// name
	// notName
	// contains
	// notContains
	// path
	// notPath
	// size
	// exclude
	// count
	private $finder;

	function __construct($path)
	{
		$this->path = $path;
		$this->finder = new Finder();
		$this->finder->in($this->path);
	}

	// need clone for avoid side-effect in many searches
	public function __call($method, $args)
	{
		$finder = clone($this->finder);
		return call_user_func_array([$finder, $method], $args);
	}

	public function dirStat()
	{
		$this->outWrap($this->path);

		$totalFilesCount = $this->files()->count();
		$filesInRoot = $this->files()->depth('== 0')->count();

		$this->out("Total files: " .   $totalFilesCount );
		$this->out("Not php files: " . $this->files()->notName('*.php')->count() );
		$this->out("Dirs in root: " .  $this->directories()->depth('== 0')->count() );
		$this->out("Files in root: " .  $filesInRoot);
		$this->out('');

		$rootDirs = [];
		foreach ($this->directories()->depth('== 0') as $dir) {
		    $dirName = $dir->getRelativePathname();
		    $filesCount = $this->files()->path('/^' . $dirName . '\//')->count();
		    $rootDirs[$dirName] = $filesCount;
		}
		arsort($rootDirs);
		
		$tableData = [];
		foreach ($rootDirs as $dirName => $count) {
			$percent = round($count / ($totalFilesCount/100), 2);
			// print only big dirs
			if($percent > 1) {
			    $tableData[] = [$dirName, $count, $percent . '%'];
			}
		}
	    $this->outTable(['Dir', 'Files', 'Percent'], $tableData);
		$this->out('');

		// $this->out("Count check: " . (array_sum($rootDirs) + $filesInRoot));
	}

	// sort
	// sortByName
	// sortByType
	// sortByAccessedTime
	// sortByChangedTime
	// sortByModifiedTime
	
	public function sort($params)
	{
	}

	public function getIterator()
	{
		return $this->finder->getIterator();
	}

	public function view()
	{
		echo $this->output;
	}

	private function outWrap($message)
	{
		$this->out(str_repeat("=", strlen($message)));
		$this->out($message);
		$this->out(str_repeat("=", strlen($message)));
	}
	private function out($message)
	{
		$this->output .= $message . "\n";
	}
	private function outTable($fields, $data)
	{
		$sizes = [];
		$header = '';
		foreach ($fields as $i => $fieldName) {
			$column = array_column($data, $i);
			$columnWidths = array_map('strlen', $column);
			$columnWidths[] = strlen($fieldName);
			$maxWidth = max( $columnWidths );
			$sizes[$i] = $maxWidth;
			$header .= str_pad($fieldName, $maxWidth) . ' | ';
		}
		$this->out(str_repeat("-", strlen($header)));
		$this->out($header);
		$this->out(str_repeat("-", strlen($header)));

		foreach ($data as $row) {
			$outRow = '';
			foreach ($row as $i => $cell) {
				$outRow .= str_pad($cell, $sizes[$i]) . ' | ';
			}
			$this->out($outRow);
		}
	}
}

// $pa = new ProjectAnalize('/data/projects/rabota.ngs.ru/scripts/Job');
// $pa->dirStat();
// $pa->view();




// run:
// /data/soft/php56/bin/php index.php

$_SERVER['NGS_REQUEST_ID'] = 1234;
$_SERVER['HTTP_HOST'] = "www.zarplata.ru.oc.d";

include '/data/projects/rabota.ngs.ru/scripts/__init.php';

try {
    if (is_null($request)) {
        $request = new Ngs_Request();
    }
    if (is_null($router)) {
        $router = new Ngs_Router($config);
    }

    $router->setHost($request->getBaseUrl());
    $router->setMethod($request->getMethod());

    $path = array();

    // Создаем страницу
    $page = new Job_Page_Admin_Entity_Vacancy($config, $request, $router);
    $page->setPath($path);

} catch (Exception $e){
	echo "\nException: " . $e->getMessage() . "\n";
	echo $e->getFile() .":". $e->getLine() . "\n";
}

if (method_exists($page, 'initEvents')) {
    $page->initEvents();
}
$page->run();
echo $page;
