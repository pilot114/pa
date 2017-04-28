<?php

use Symfony\Component\Finder\Finder;

class FileAnalize
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

    public function __call($method, $args)
    {
        // need clone for avoid side-effect in many searches
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
        foreach ($this->files()->notName('*.php') as $file) {
            $this->out('* ' . $file->getRelativePathname());
        }
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
        $this->out('Big dirs: (>1% file count)');
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

        $this->out('Big files: (>32kb)');
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

    public function getFiles($subpath)
    {
        $subpath = str_replace('/', '\/', $subpath);
        return $this->files()->path('/^' . $subpath . '\//');
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
