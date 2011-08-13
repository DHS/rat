<?php

// Config file contains lots of handy variables
require_once 'config/config.php';

// Save config vars for later
$app_vars = $app;
unset($app);

// Define app class
class app {
	
	function __construct() {

		// Load models
		$handle = opendir('models');
		while (false !== ($file = readdir($handle))) {
			$model = substr($file, 0, -4);
			if ($file[0] != '.') {
				include "models/$model.php";
				$this->$model = new $model;
			}
		}

	}
	
	function loadView($view) {
		
		include "themes/$app->theme/$view.php";
		
	}
	
}

// Create new instance of app
$app = new app;

// Add app vars back in
foreach ($app_vars as $key => $value) {
	$app->$key = $value;
}

// Setup database
require_once 'config/database.php';

// Start session
session_start();

// Finds page name
preg_match("/[a-zA-Z0-9]+\.php/", $_SERVER['PHP_SELF'], $result);

// If user is logged out, app is private and page is not in public_pages then show splash page
if ($_SESSION['user'] == NULL && $app->private == TRUE && in_array($result[0], $app->public_pages) == FALSE) {

	if (count($app->admin->list_users()) == 0 && $result[0] == 'admin.php') {

		// Make an exception for setup
		
		// So at the moment, setup requires $app->private to be TRUE
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

?>