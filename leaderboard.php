<?php

require_once 'config/init.php';

//	Critical: Feature must be enabled

if (!isset($app->plugins->points) || $app->plugins->points['leaderboard'] == FALSE) {
	$page['name'] = 'Page not found';
	include 'themes/'.$app->config->theme.'/header.php';
	include 'themes/'.$app->config->theme.'/footer.php';
	exit;
}

// Header

$page['name'] = 'Leaderboard';
include 'themes/'.$app->config->theme.'/header.php';

// Show leaderboard

$leaderboard = 	$app->plugins->points->get_leaderboard(20);
include 'themes/'.$app->config->theme.'/leaderboard.php';

// Footer

include 'themes/'.$app->config->theme.'/footer.php';

?>