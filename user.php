<?php

require_once 'config/init.php';

if (isset($_GET['id'])) {
	$user = $app->user->get($_GET['id']);
} elseif (isset($_SESSION['user'])) {
	$user = $app->user->get($_SESSION['user']['id']);
} else {
	$user = NULL;
}

// Critical: user must exist

if ($user == NULL) {

	$app->page->name = 'User not found';
	$app->loadView('header');
	$app->loadView('footer');
	exit();

}

// Header

if (isset($app->plugins->gravatar))
	$app->page->title_gravatar = $user['email'];

$app->page->head_title['head_title'] = $user['name'].' on '.$app->config->name;
$app->page->title = '<a href="user.php?id='.$user['id'].'">'.$user['name'].'</a> on <a href="index.php">'.$app->config->name.'</a>';

$app->loadView('header');

// Show profile

$app->loadView('user_profile');

// Show follow button

if ($app->config->friends->enabled == TRUE)
	$app->loadView('friends_button');

// Show number of points

if (isset($app->plugins->points))
	$app->loadView('points');

// Show new item form

//if ($_SESSION['user']['post_permission'] == 1)
//	$app->loadView('items_add');

// List all items for this user
$app->page->items = $app->item->list_user($user['id']);

if (count($app->page->items) > 0) {
	
	$app->loadView('items_list_user');
	
} else {
	
	// If own page and no post_permission OR someone else's page show 'no articles yet'
	if (($_SESSION['user']['id'] == $user['id'] && $_SESSION['user']['post_permission'] == 0) || $_SESSION['user']['id'] != $user['id'])
		echo '<p>'.$user['username'].' hasn\'t published any '.$app->config->items['name_plural'].' yet.</p>';
	
}

// Footer

$app->loadView('footer');

?>