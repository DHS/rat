<?php

class leaderboard {
	
	function index() {
		
		//	Critical: Feature must be enabled

		if (!isset($app->plugins->points) || $app->plugins->points['leaderboard'] == FALSE) {
			$app->page->name = 'Page not found';
			$app->loadView('header');
			$app->loadView('footer');
			exit;
		}

		// Header

		$app->page->name = 'Leaderboard';
		$app->loadView('header');

		// Show leaderboard

		$app->page->leaderboard = 	$app->plugins->points->get_leaderboard(20);
		$app->loadView('leaderboard');

		// Footer

		$app->loadView('footer');
		
	}
	
}

?>