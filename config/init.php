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
	
}

// Create new instance of app
$app = new app;

// Determine whether site is dev or live
$domain = substr(substr($app->config->url, 0, -1), 7);

if ($_SERVER['HTTP_HOST'] == $domain || $_SERVER['HTTP_HOST'] == 'www.'.$domain) {
	define('SITE_IDENTIFIER', 'live');
} else {
	define('SITE_IDENTIFIER', 'dev');
}

// Setup database
require_once 'database.php';

// Start session
session_start();

?>