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
	include 'themes/'.$app->theme.'/header.php';
	include 'themes/'.$app->theme.'/footer.php';
	exit();

}

// Header

if (isset($GLOBALS['gravatar']))
	$app->page_title_gravatar = $user['email'];

$page['head_title'] = $user['name'].' on '.$app->name;
$page['title'] = '<a href="user.php?id='.$user['id'].'">'.$user['name'].'</a> on <a href="index.php">'.$app->name.'</a>';

include 'themes/'.$app->theme.'/header.php';

// Show profile

include 'themes/'.$app->theme.'/user_profile.php';

// Show follow button

if ($app->friends->enabled == TRUE)
	include 'themes/'.$app->theme.'/friends_button.php';

// Show number of points

if (is_object($GLOBALS['points']))
	include 'themes/'.$app->theme.'/points.php';

// Show new item form

//if ($_SESSION['user']['post_permission'] == 1)
//	include 'themes/'.$app->theme.'/items_add.php';

// List all items for this user
$items = $app->item->list_user($user['id']);

if (count($items) > 0) {
	
	include 'themes/'.$app->theme.'/items_list_user.php';

} else {
	
	// If own page and no post_permission OR someone else's page show 'no articles yet'
	if (($_SESSION['user']['id'] == $user['id'] && $_SESSION['user']['post_permission'] == 0) || $_SESSION['user']['id'] != $user['id'])
		echo '<p>'.$user['username'].' hasn\'t published any '.$GLOBALS['app']['items']['name_plural'].' yet.</p>';
	
}

// Footer

include 'themes/'.$app->theme.'/footer.php';

?>