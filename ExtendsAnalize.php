<?php

class ExtendsAnalize extends AnalizeAbstract
{
	private $tree = [];

	function setClassToTree($childClass, $parentClass)
	{
	    $iterator  = new RecursiveArrayIterator($this->tree);
	    $recursive = new RecursiveIteratorIterator(
	        $iterator,
	        RecursiveIteratorIterator::SELF_FIRST
	    );
	    foreach ($recursive as $key => $value) {
	        if ($key === $parentClass) {
        		$recursive[$parentClass][] = $childClass;
	        }
	    }
	}

	private function createTree($flat)
	{
		// остались классы, чьи родители не найдены ;-(

		foreach ($flat as $i => $classes) {
			if (count($classes) == 1) {
				$tree[$classes[0]] = [];
				unset($flat[$i]);
			} else {
				if (isset($tree[$classes[1]])) {
					$tree[$classes[1]][] = $classes[0];
					unset($flat[$i]);
				} else {
					$tree[$classes[1]] = $this->createTree($tree[$classes[1]], $flat);
				}
			}
		}
	}

	public function buildTree()
	{
		$flat = [];
		foreach ($this->files()->name('*.php') as $file) {
			foreach ($file->openFile() as $string) {
				if (strpos($string, 'class ') === 0) {
					$string = str_replace('class ', '', $string);
					$string = explode(' implements ', $string)[0];

					$flat[] = array_map(function($class){
						return trim($class, "\t\n\r\0\x0B\\");
					}, explode(' extends ', $string));
				}
			}
		}

		foreach ($flat as $classes) {
			if (count($classes) == 1) {
				$this->tree[$classes[0]] = [];
			} else {
				$this->setClassToTree($classes[0], $classes[1]);
			}
		}


		var_dump($this->tree);
		die();
	}

	public function getTree()
	{
		return $this->tree;
	}
}