<?php

session_start();

require_once 'lib/application.php';
$app = new Application();

require_once 'lib/mysql.php';

// Get request from server, split into segments, store as controller, view, id and params
$request = substr($_SERVER['REQUEST_URI'], strlen(BASE_DIR));

// Split at '.' and before '?' to obtain request format
$segments = preg_split("/\./", $request);
$format = preg_split("/\?/", $segments[1]);
$format = $format[0];

// Split request at each '/' to obtain route
$segments = preg_split("/\//", $segments[0]);

$uri = array(	'controller'	=> $segments[1],
				'action'		=> $segments[2],
				'id'			=> $segments[3],
				'format'		=> $format,
				'params'		=> $_GET
			);

// Set the controller to the default if not in URI
if (empty($uri['controller']))
	$uri['controller'] = $app->config->default_controller;

// Request the URI
$app->request($uri);

?>
