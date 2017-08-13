<?php

use Symfony\Component\Finder\Finder as TrueFinder;

class Finder
{
    protected $path;
    protected $output;
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
    // sort
    // sortByName
    // sortByType
    // sortByAccessedTime
    // sortByChangedTime
    // sortByModifiedTime
    protected $finder;

    function __construct($path)
    {
        $this->path = $path;
        $this->finder = new TrueFinder();
        $this->finder->in($this->path);
    }

    public function __call($method, $args)
    {
        // need clone for avoid side-effect in many searches
        $finder = clone($this->finder);
        return call_user_func_array([$finder, $method], $args);
    }

    public function getIterator()
    {
        return $this->finder->getIterator();
    }

    public function getFiles($subpath)
    {
        $subpath = str_replace('/', '\/', $subpath);
        return $this->files()->path('/^' . $subpath . '\//');
    }

    public function view()
    {
        return $this->output;
    }

    protected function outWrap($message)
    {
        $this->out(str_repeat("=", strlen($message)));
        $this->out($message);
        $this->out(str_repeat("=", strlen($message)));
    }

    protected function out($message)
    {
        $this->output .= $message . "\n";
    }

    protected function outTable($fields, $data)
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