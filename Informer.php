<?php

class Informer
{
	function __construct(FileAnalize $fa, ClassAnalize $ca, ServiceAnalize $sa)
	{
		$this->fa = $fa;
		$this->ca = $ca;
		$this->sa = $sa;

		$this->ca->buildTree();
	}

	public function byClass($className)
	{
		$classInfo = [
			'class' => $className,
			'service' => null,
			'inheritanceTree' => $this->ca->findInheritanceTree($className),
			'whereService' => [],
			'usedService' => [],
			'usedAsClass' => []
		];
		if (in_array($className, $this->sa->getListClasses())) {
			$serviceName = $this->sa->getServiceName($className);
			$classInfo['service'] = $serviceName;

			$classInfo['whereService'] = $this->sa->getWhereService($serviceName);
			$classInfo['usedService'] = $this->sa->getUsedService($serviceName);

			// test
			$className = 'Job_Api_Exception_BadRequest';
			$findedStrings = $this->fa->findBySignature("/new $className\(/", '/^class /');

			foreach ($findedStrings as $string) {
				$class = $this->ca->extractClassesFromString($string)[0];
				$classInfo['usedAsClass'][] = $class;
			}
		}
		return $classInfo;
	}
}