<?php

// Start session
//session_start();

// Prepare config classes, ready to be loaded inside application class
require_once 'config/server.php';
require_once 'config/application.php';

$config = new AppConfig;

require_once 'lib/application.php';

// Get request from server, split into segments, store as controller, view, id and params
$request = substr($_SERVER['REQUEST_URI'], strlen('/rat-private'));

// Split at '.' and before '?' to obtain request format
$segments = preg_split("/\./", $request);
$format = preg_split("/\?/", $segments[1]);
$format = $format[0];

// Split request at each '/' to obtain route
$segments = preg_split("/\//", $segments[0]);

// Set up uri variable to pass to app
$uri = array(	'controller'	=> $segments[1],
				'action'		=> $segments[2],
				'id'			=> $segments[3],
				'format'		=> $format,
				'params'		=> $_GET
			);

// Set the controller to the default if not in URI
if (empty($uri['controller']))
	$uri['controller'] = $config->default_controller;

Application::initialise($uri, $config);

?>
