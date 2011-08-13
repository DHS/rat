<?php

// Define app class
class app {
	
	function __construct() {
		
		// Load config
		$this->loadConfig();
		
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
	
	function loadConfig() {
		
		require_once 'config/config.php';
		$this->config = new config;
		
		// Determine whether site is dev or live
		$domain = substr(substr($url, 0, -1), 7);
		
		if ($_SERVER['HTTP_HOST'] == $domain || $_SERVER['HTTP_HOST'] == 'www.'.$domain) {
			define('SITE_IDENTIFIER', 'live');
		} else {
			define('SITE_IDENTIFIER', 'dev');
		}
		
	}
	
	function loadView($view) {
		
		global $app;
		
		include "themes/{$app->config->theme}/$view.php";
		
	}
	
}

// Create new instance of app
$app = new app;

// Setup database
require_once 'config/database.php';

// Start session
session_start();
/*
// Finds page name
preg_match("/[a-zA-Z0-9]+\.php/", $_SERVER['PHP_SELF'], $result);

// If user is logged out, app is private and page is not in public_pages then show splash page
if ($_SESSION['user'] == NULL && $app->config->private == TRUE && in_array($result[0], $app->config->public_pages) == FALSE) {

	if (count($app->admin->list_users()) == 0 && $result[0] == 'admin.php') {

		// Make an exception for setup
		
		// So at the moment, setup requires $app->config->private to be TRUE
		// and admin.php must NOT be in public_pages
		
	} else {

		// Show splash page
		$app->loadView('header');
		$app->loadView('splash');
		$app->loadView('footer');

		// Stop processing the rest of the page
		exit();		
		
	}

}
*/
?>