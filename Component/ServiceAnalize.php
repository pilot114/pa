<?php

class ServiceAnalize
{
	private $container = [];

	public function __construct($containerFile)
	{
		$this->container = include($containerFile);
	}

	// для статистики - показывает какие ключи есть в описаниях сервисов
	public function getInfo() : string
	{
		$info = ['keys' => [], 'count' => 0];
		$info['count'] = count($this->container);
		foreach ($this->container as $serviceName => $service) {
			$info['keys'] = array_merge($info['keys'], array_keys($service));
		}
		$info['keys'] = array_count_values($info['keys']);

		return $info;
	}

	public function getListClasses() : array
	{
		// убираем начальный слэш у некоторых классов
		return array_map(function($class){
			return trim($class, '\\');
		}, array_column($this->container, 'class'));
	}

	public function getServiceNameByClassName($className) : string
	{
		return array_keys(array_filter($this->container , function($service, $serviceName) use ($className) {
			return ($service['class'] == $className);
		}, ARRAY_FILTER_USE_BOTH))[0];
	}

	public function getServicesWhereUsed($serviceName) : array
	{
		$findedService = [];
		foreach ($this->container as $serviceNameCurrent => $service) {

			if (isset($service['args']) && in_array('$'.$serviceName, $service['args'])) {
				$findedService[$serviceNameCurrent] = $service;
			}
		}
		return $findedService;
	}

	public function getServicesFromArgs($serviceName) : array
	{
		if (!isset($this->container[$serviceName])) {
			return [];
		}

		$services = [];

		$service = $this->container[$serviceName];
		$args = $service['args'];

		foreach ($args as $serviceName) {
			$serviceName = trim($serviceName, '$');
			$services[$serviceName] = $this->container[$serviceName];

			// выводить factory мы не хотим
			if (isset($services[$serviceName]['factory'])) {
				unset($services[$serviceName]['factory']);
			}
		}

		return $services;
	}
}