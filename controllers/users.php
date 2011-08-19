<?php

class UsersController {
	
	// Show a list of users
	function index() {
		
		global $app;
		
		// Not needed?
		
	}
	
	// Add a user
	function add() {
		
		global $app;
		
		$app->loadLayout('users/add');
		
	}
	
	// Show a user
	function show($id) {
		
		global $app;
		
		$app->page->user = $app->user->get($id);
		$app->page->items = $app->item->list_user($id);
		
		$app->loadLayout('users');
		
	}
	
	function update($id) {
		
		global $app;
		
		$app->page->user = $app->user->get($id);
		
		$app->loadLayout('users/update');
		
	}
	
	function json($username) {
		
		global $app;
		
		$user['user'] = $app->user->get_by_username($username);
		$user['items'] = $app->item->list_user($user['user']['id']);
		$app->page->json = $user;
		$app->loadView('json');
		
	}
	
}

?>