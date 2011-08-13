<?php

require_once 'config/init.php';

//	Critical: Feature must be enabled

if (!isset($GLOBALS['points']) || $app->points['leaderboard'] == FALSE) {
	$page['name'] = 'Page not found';
	include 'themes/'.$app->theme.'/header.php';
	include 'themes/'.$app->theme.'/footer.php';
	exit;
}

// Header

$page['name'] = 'Leaderboard';
include 'themes/'.$app->theme.'/header.php';

// Show leaderboard

$leaderboard = 	$GLOBALS['points']->get_leaderboard(20);
include 'themes/'.$app->theme.'/leaderboard.php';

// Footer

include 'themes/'.$app->theme.'/footer.php';

?>