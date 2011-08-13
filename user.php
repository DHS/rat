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

	$page['name'] = 'User not found';
	include 'themes/'.$app->config->theme.'/header.php';
	include 'themes/'.$app->config->theme.'/footer.php';
	exit();

}

// Header

if (isset($GLOBALS['gravatar']))
	$app->page_title_gravatar = $user['email'];

$page['head_title'] = $user['name'].' on '.$app->config->name;
$page['title'] = '<a href="user.php?id='.$user['id'].'">'.$user['name'].'</a> on <a href="index.php">'.$app->config->name.'</a>';

include 'themes/'.$app->config->theme.'/header.php';

// Show profile

include 'themes/'.$app->config->theme.'/user_profile.php';

// Show follow button

if ($app->config->friends->enabled == TRUE)
	include 'themes/'.$app->config->theme.'/friends_button.php';

// Show number of points

if (isset($GLOBALS['points']))
	include 'themes/'.$app->config->theme.'/points.php';

// Show new item form

//if ($_SESSION['user']['post_permission'] == 1)
//	include 'themes/'.$app->config->theme.'/items_add.php';

// List all items for this user
$items = $app->item->list_user($user['id']);

if (count($items) > 0) {
	
	include 'themes/'.$app->config->theme.'/items_list_user.php';

} else {
	
	// If own page and no post_permission OR someone else's page show 'no articles yet'
	if (($_SESSION['user']['id'] == $user['id'] && $_SESSION['user']['post_permission'] == 0) || $_SESSION['user']['id'] != $user['id'])
		echo '<p>'.$user['username'].' hasn\'t published any '.$app->config->items['name_plural'].' yet.</p>';
	
}

// Footer

include 'themes/'.$app->config->theme.'/footer.php';

?>