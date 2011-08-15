<?php

// Define app class
class app {
	
	function __construct() {
		
		// Load config
		require_once 'config.php';
		$this->config = new config;
		
		// Load models
		$handle = opendir('models');
		while (false !== ($file = readdir($handle))) {
			$model = substr($file, 0, -4);
			if ($file[0] != '.') {
				include "models/$model.php";
				$this->$model = new $model;
			}
		}
		
		// Load plugins
		foreach ($this->config->plugins as $key => $value) {
			if ($value == TRUE) {
				include "plugins/$key.php";
				$this->plugins->$key = new $key;
			}
		}
		
	}
	
	function loadView($view) {
		
		global $app;
		
		include "themes/{$app->config->theme}/$view.php";
		
	}
	
	function loadController($controller) {
		
		global $app;
		
		include "controllers/$controller.php";
		return new $controller;
		
	}
	
}

?>