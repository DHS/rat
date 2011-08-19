<?php

class UsersController {
	
	// Show a list of users
	function index() {
		
		global $app;
		
		
		
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
	
	function json($username) {
		
		global $app;
		
		$user['user'] = $app->user->get_by_username($username);
		$user['items'] = $app->item->list_user($user['user']['id']);
		$app->page->json = $user;
		$app->loadView('json');
		
	}
	
}

/*
class users {
	
	function show($username, $page = 1) {
		
		global $app;
		
		$limit = 2;
		$offset = ($page - 1) * $limit;
		
		$app->page->user = $app->user->get_by_username($username);
		$app->page->items = $app->item->list_user($app->page->user['id'], $limit, $offset);
		
		// Header

		if (isset($app->plugins->gravatar))
			$app->page->title_gravatar = $app->page->user['email'];

		$app->page->head_title = $app->page->user['name'].' on '.$app->config->name;
		$app->page->title = '<a href="/'.$app->page->user['username'].'">'.$app->page->user['name'].'</a> on <a href="/">'.$app->config->name.'</a>';

		$app->loadView('header');

		// Show profile

		$app->loadView('users/profile');

		// Show follow button

		if ($app->config->friends['enabled'] == TRUE)
			$app->loadView('friends_button');

		// Show number of points

		if (isset($app->plugins->points))
			$app->loadView('points');

		// Show new item form

		//if ($_SESSION['user']['post_permission'] == 1)
		//	$app->loadView('items_add');

		// List all items for this user

		if (count($app->page->items) > 0) {

			$app->loadView('items_list_user');

		} else {

			// If own page and no post_permission OR someone else's page show 'no articles yet'
			if (($_SESSION['user']['id'] == $app->page->user['id'] && $_SESSION['user']['post_permission'] == 0) || $_SESSION['user']['id'] != $app->page->user['id'])
				echo '<p>'.$app->page->user['username'].' hasn\'t published any '.$app->config->items['name_plural'].' yet.</p>';

		}

		// Footer

		$app->loadView('footer');
		
	}
	
	function json($username) {
		
		global $app;
		
		$user['user'] = $app->user->get_by_username($username);
		$user['items'] = $app->item->list_user($user['user']['id']);
		$app->page->json = $user;
		$app->loadView('json');
		
	}
	
}
*/
?>