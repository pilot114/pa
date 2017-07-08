<?php

class ServiceAnalize
{
	private $container;
	private $containerInfo;

	public function __construct($containerFile)
	{
		$this->container = include($containerFile);

		$info = ['keys' => [], 'count' => 0];
		$info['count'] = count($this->container);
		foreach ($this->container as $serviceName => $service) {
			$info['keys'] = array_merge($info['keys'], array_keys($service));
		}
		$info['keys'] = array_count_values($info['keys']);

		$classes = array_column($this->container, 'class');
		$this->containerInfo = $info;
	}

	public function getInfo()
	{
		return $this->containerInfo;
	}

	public function getListClasses()
	{
		// убираем начальный слэш у некоторых классов
		return array_map(function($class){
			return trim($class, '\\');
		}, array_column($this->container, 'class'));
	}

	public function getServiceName($className)
	{
		return array_keys(array_filter($this->container , function($service, $serviceName) use ($className) {
			return ($service['class'] == $className);
		}, ARRAY_FILTER_USE_BOTH))[0];
	}

	// находим где используется сервис
	public function getWhereUsedService($serviceName)
	{
		$findedService = [];
		foreach ($this->container as $serviceNameCurrent => $service) {

			if (isset($service['args']) && in_array('$'.$serviceName, $service['args'])) {
				$findedService[$serviceNameCurrent] = $service;
			}
		}
		return $findedService;
	}

	// находим кто использует сервис
	public function getWhoUsesService($serviceName)
	{
		$services = [];
		if (isset($this->container[$serviceName])) {
			$depends = $this->container[$serviceName]['args'];

			foreach ($depends as $serviceName) {
				$serviceName = trim($serviceName, '$');
				$services[$serviceName] = $this->container[$serviceName]['class'];
			}
			$container = $this->container;
			$depends = array_map(function($class) use ($container){
				return trim($class, '$');
			}, $depends);
		}

		return $services;
	}
}