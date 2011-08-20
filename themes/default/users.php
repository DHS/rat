<?php

// Show profile

$app->loadView('users/profile');

// Show follow button

if ($app->config->friends['enabled'] == TRUE)
	$app->loadView('friends_button');

// Show number of points

if (isset($app->plugins->points))
	$app->plugins->points->view();

// Show new item form

//if ($_SESSION['user']['post_permission'] == 1)
//	$app->loadView('items_add');

// List all items for this user

if (count($app->page->items) > 0) {

	$app->loadView('items/user');

} else {

	// If own page and no post_permission OR someone else's page show 'no articles yet'
	if (($_SESSION['user']['id'] == $app->page->user['id'] && $_SESSION['user']['post_permission'] == 0) || $_SESSION['user']['id'] != $app->page->user['id'])
		echo '<p>'.$app->page->user['username'].' hasn\'t published any '.$app->config->items['name_plural'].' yet.</p>';

}

?>