<?php
require './vendor/autoload.php';

use Symfony\Component\Finder\Finder;

/*

Project Helper - утилита для анализа и обслуживания кода проекта.

Analize - статический анализ структуры проекта и php-кода
(часть проекта для активного анализа указывается в конфиге)

проверяет соотвествие стандарту и фиксает что может:
https://github.com/squizlabs/PHP_CodeSniffer

./vendor/bin/phpcs -h
./vendor/bin/phpcbf -h

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

        $this->out("Total files: " .   $totalFilesCount);
        $this->out("Not php files: " . $this->files()->notName('*.php')->count());
        $this->out("Dirs in root: " .  $this->directories()->depth('== 0')->count());
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
        $this->out('Big dirs:');
        $this->outTable(['Dir', 'Files', 'Percent'], $tableData);
        $this->out('');

        // for test:
        // $this->out("Count check: " . (array_sum($rootDirs) + $filesInRoot));
    }

    public function fileStat()
    {
        $bigFiles = [];
        foreach ($this->files() as $file) {
            if ($file->getSize() > 1024 * 32) {
                $bigFiles[] = [(int)($file->getSize()/1024), $file->getRelativePathname()];
            }
            ksort($bigFiles);
        }
        usort(
            $bigFiles, function ($a, $b) {
                return $a[0] < $b[0];
            }
        );

        $this->out('Big files:');
        $this->outTable(['Size, kb', 'Name'], $bigFiles);
        $this->out('');
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
        // add index column
        array_unshift($fields, '#');
        $numberRow = 1;
        $data = array_map(
            function ($row) use (&$numberRow) {
                array_unshift($row, $numberRow++);
                return $row;
            }, $data
        );

        $sizes = [];
        $header = '';
        foreach ($fields as $i => $fieldName) {
            $column = array_column($data, $i);
            $columnWidths = array_map('strlen', $column);
            $columnWidths[] = strlen($fieldName);
            $maxWidth = max($columnWidths);
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

$pa = new ProjectAnalize('/home/oleg/sources/job/scripts/Job');
$pa->dirStat();
$pa->fileStat();
$pa->view();
