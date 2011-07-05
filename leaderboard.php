<?php

require_once 'config/init.php';

//	Critical: Feature must be enabled

if (!is_object($GLOBALS['points']) || $app['points']['leaderboard'] == FALSE) {
	$page['name'] = 'Page not found';
	include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
	include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';
	exit;
}

// Header

$page['name'] = 'Leaderboard';
include 'themes/'.$GLOBALS['app']['theme'].'/header.php';

// Show leaderboard

$leaderboard = 	$GLOBALS['points']->get_leaderboard(20);
include 'themes/'.$GLOBALS['app']['theme'].'/leaderboard.php';

// Footer

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>