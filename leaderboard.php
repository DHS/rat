<?php

require_once 'config/init.php';

//	Critical: Feature must be enabled

if (!isset($GLOBALS['points']) || $app->points['leaderboard'] == FALSE) {
	$page['name'] = 'Page not found';
	$app->loadView('header');
	$app->loadView('footer');
	exit;
}

// Header

$page['name'] = 'Leaderboard';
$app->loadView('header');

// Show leaderboard

$leaderboard = 	$GLOBALS['points']->get_leaderboard(20);
$app->loadView('leaderboard');

// Footer

$app->loadView('footer');

?>