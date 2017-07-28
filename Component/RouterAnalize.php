<?php

use Noodlehaus\Config;

class RouterAnalize
{
	protected $routes;

	function __construct($path)
	{
		// костыль - некоторые конфиги в проекте косячные
		// $configs = new Config(glob($path . '/*'));
		$config = new Config($path . '/api_v1.yml');
		$this->routes = $config->get('Ngs_Router.routes.0.routes');
	}
}