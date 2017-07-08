<?php

class ClassAnalize extends AbstractFinderAnalize
{
	private $classes = [];
	private $tree = [];

	// stupid  - only 5 deep classes
	private function findAndSetLeave($maybeChildClass)
	{
		$finded = false;
		foreach ($this->tree as $name => $childs) {
			if (isset($childs[$maybeChildClass])) {
				// перемещаем из рута в настоящего родителя
				$this->tree[$name][$maybeChildClass] = $this->tree[$maybeChildClass];
				$finded = true;
			}
			if (count($childs) > 0) {
				foreach ($childs as $name2 => $childs2) {
					if (isset($childs2[$maybeChildClass])) {

						$this->tree[$name][$name2][$maybeChildClass] = $this->tree[$maybeChildClass];
						$finded = true;
					}
					if (count($childs2) > 0) {
						foreach ($childs2 as $name3 => $childs3) {
							if (isset($childs3[$maybeChildClass])) {

								$this->tree[$name][$name2][$name3][$maybeChildClass] = $this->tree[$maybeChildClass];
								$finded = true;
							}
						}
					}
				}
			}
		}
		if ($finded) {
			unset($this->tree[$maybeChildClass]);
		}
	}

	public function extractClassesFromString($string)
	{
		$string = str_replace('class ', '', $string);
		$string = explode(' implements ', $string)[0];

		return array_map(function($class){
			return trim($class, "\t\n\r\0\x0B\\{ ");
		}, explode(' extends ', $string));
	}

	public function buildTree()
	{
		foreach ($this->files()->name('*.php') as $file) {
			foreach ($file->openFile() as $string) {
				if (strpos($string, 'class ') === 0) {
					$classes = $this->extractClassesFromString($string);

					$this->classes[] = $classes[0];

					if (count($classes) == 1) {
						// класс ни от кого не наследуется, всегда будет в корне
						if (!isset($this->tree[$classes[0]])) {
							$this->tree[$classes[0]] = [];
						}
					} else {
						$this->tree[$classes[1]][$classes[0]] = [];
					}
				}
			}
		}

		// редуцируем дерево
		foreach ($this->tree as $class => $value) {
			if ($value) {
				$this->findAndSetLeave($class);
			}
		}
	}

	public function getTree()
	{
		return $this->tree;
	}

	public function getClasses()
	{
		return $this->classes;
	}

	public function findInheritanceTree($className)
	{
		foreach ($this->tree as $name => $val) {
			if ($name == $className) {
				return [$name => $this->tree[$name]];
			}
			$iterator  = new RecursiveArrayIterator($this->tree[$name]);
		    $recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
		    foreach ($recursive as $key => $value) {
		        if ($key === $className) {
		            return [$name => $this->tree[$name]];
		        }
		    }
		}
	}
}