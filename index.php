<?php

session_start();

require_once 'lib/application.php';

// Load config early to get base_dev_dir and default_controller
require_once 'config/config.php';
$config = new config;

// Get request from server, split into segments, store as controller, view, id and params
$request = substr($_SERVER['REQUEST_URI'], strlen($config->dev_base_dir));

// Split at '.' and before '?' to obtain request format
$segments = preg_split("/\./", $request);
$format = preg_split("/\?/", $segments[1]);
$format = $format[0];

// Split request at each '/' to obtain route
$segments = preg_split("/\//", $segments[0]);

$uri = array(	'controller'	=> $segments[0],
				'view'			=> $segments[1],
				'id'			=> $segments[2],
				'format'		=> $format,
				'params'		=> $_GET
			);

if (empty($uri['controller']))
	$uri['controller'] = $config->default_controller;

// Instantiate a new application
$app = new application($uri);

// Load database config
require_once 'lib/database.php';

// Load the controller
$app->loadController($uri['controller']);

?>