<?php

class SessionsController {
	
	function index() {
		
		global $app;
		
		// Not needed?
		
	}
	
	function add() {
		
		global $app;
		
		if (empty($_SESSION['user'])) {
			$app->loadLayout('sessions/add');
		} else {
			$app->page->message = 'You are already logged in!<br /><a href="logout.php">Click here</a> to logout.';
			$app->loadLayout('message');
		}
		
	}
	
	function remove() {
		
		global $app;
		
		
		
	}
	
}

?>