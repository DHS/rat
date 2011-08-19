<?php

class LeaderboardController {
	
	function index() {
		
		global $app;
		
		//	Critical: Feature must be enabled

		if (!isset($app->plugins->points) || $app->plugins->points['leaderboard'] == FALSE) {
			$app->page->name = 'Page not found';
			$app->loadPartial('header');
			$app->loadPartial('footer');
			exit;
		}

		// Show leaderboard

		$app->page->leaderboard = 	$app->plugins->points->get_leaderboard(20);
		$app->loadLayout('leaderboard');
		
	}
	
}

?>
