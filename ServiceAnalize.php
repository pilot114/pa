<?php

class ServiceAnalize
{
	private $container;
	private $containerInfo;

	public function __construct($containerFile)
	{
		$this->container = include($containerFile);

		$info = ['keys' => [], 'count' => 0];
		foreach ($this->container as $serviceName => $service) {
			$info['keys'] = array_merge($info['keys'], array_keys($service));
		}
		$info['keys'] = array_count_values($info['keys']);

		$classes = array_column($this->container, 'class');
		$info['count'] = count($classes);
		$this->containerInfo = $info;
	}


	public function getInfo()
	{
		return $this->containerInfo;
	}

	public function getListClasses()
	{
		return array_column($this->container, 'class');
	}
}