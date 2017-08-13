<?php

class ClassReport
{
	function __construct(FsAnalize $fa, ClassAnalize $ca, ServiceAnalize $sa)
	{
		$this->fa = $fa;
		$this->ca = $ca;
		$this->sa = $sa;

		$this->ca->buildTree();
	
		$this->classes = [];
	}

	public function addClass($className)
	{
		$this->classes[$className] = null;
	}

	public function build()
	{
		foreach ($this->classes as $className => $_) {

			$classInfo = [
				'service' => [
					'name' => null,
					'whereUsed' => [],
					'args' => [],
				],
				'inheritanceTree' => $this->ca->findInheritanceTree($className),
				'new' => []
			];
			
			if (in_array($className, $this->sa->getListClasses())) {
				$serviceName = $this->sa->getServiceNameByClassName($className);

				$classInfo['service']['name'] = $serviceName;
				$classInfo['service']['whereUsed'] = $this->sa->getServicesWhereUsed($serviceName);
				$classInfo['service']['args'] = $this->sa->getServicesFromArgs($serviceName);
			}

			// дополнительно, ищем где класс создается через new
			$findedStrings = $this->fa->findBySignature("/new $className\(/", '/^class /');
			foreach ($findedStrings as $string) {
				$class = $this->ca::extractClassesFromString($string)[0];
				$classInfo['new'][] = $class;
			}
			$this->classes[$className] = $classInfo;
		}

		return $this->classes;
	}
}