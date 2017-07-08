<?php

class Informer
{
	function __construct(FileAnalize $fa, ClassAnalize $ca, ServiceAnalize $sa)
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
					'whoUses' => [],
				],
				'inheritanceTree' => $this->ca->findInheritanceTree($className),
				'whereUsed' => []
			];
			
			if (in_array($className, $this->sa->getListClasses())) {
				$serviceName = $this->sa->getServiceName($className);

				$classInfo['service']['name'] = $serviceName;
				$classInfo['service']['whereUsed'] = $this->sa->getWhereUsedService($serviceName);
				$classInfo['service']['whoUses'] = $this->sa->getWhoUsesService($serviceName);
			}

			$findedStrings = $this->fa->findBySignature("/new $className\(/", '/^class /');
			foreach ($findedStrings as $string) {
				$class = $this->ca->extractClassesFromString($string)[0];
				$classInfo['whereUsed'][] = $class;
			}
			$this->classes[$className] = $classInfo;
		}

		return $this->classes;
	}
}