<?php

class Filter {

	public function __construct(&$app) {
		$this->app = $app;
	}

	public function requireLoggedIn($uri, $actions) {
		
		if (in_array($uri['action'], $actions) && ! isset($_SESSION['user_id'])) {
			Application::flash('error', 'You must be logged in!');
			$this->app->redirect_to('sessions', 'add');
			exit;
		}
		
	}

	public function requireLoggedOut($uri, $actions) {
		
		if (in_array($uri['action'], $actions) && isset($_SESSION['user_id'])) {
			Application::flash('error', 'You are already logged in!');
			$this->app->redirect_to('items');
			exit;
		}
		
	}
	
	public function requireInvitesEnabled($uri, $actions) {
		
		if (in_array($uri['action'], $actions) && $this->app->config->invites['enabled'] != TRUE) {
			throw new RoutingException($uri, "Page not found");
		}
		
	}
	
	public function requireAdmin($uri, $actions) {
		
		if (in_array($uri['action'], $actions) && !in_array($_SESSION['user_id'], $this->app->config->admin_users)) {
			throw new RoutingException($uri, "Page not found");
		}
		
	}

	// Add your own filters to run before each action is loaded

}

?>