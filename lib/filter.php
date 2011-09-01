<?php

class Filter {

	public function __construct(&$app) {
		$this->app = $app;
	}

	public function requireLoggedIn($uri, $actions) {
		
		if (in_array($uri['action'], $actions) && ! isset($_SESSION['user_id'])) {
			Application::flash('error', "You must be logged in!");
			$this->app->redirect_to('sessions', 'add');
		}

	}

	public function requireLoggedOut($uri, $actions) {

		if (in_array($uri['action'], $actions) && isset($_SESSION['user_id'])) {
			Application::flash('error', "You are already logged in!");
			$this->app->redirect_to('items');
		}

	}

	// Add your own filters to run before each action is loaded

}

?>
