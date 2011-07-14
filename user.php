<?php

require_once 'config/init.php';

if ($_GET['id']) {
	$user = user_get_by_id($_GET['id']);
} elseif ($_SESSION['user']['id']) {
	$user = user_get_by_id($_SESSION['user']['id']);
} else {
	$user = NULL;
}

// Critical: user must exist

if ($user == NULL) {

	$page['name'] = 'User not found';
	include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
	include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';
	exit();

}

// Header

if (is_object($GLOBALS['gravatar']))
	$app['page_title_gravatar'] = $user['email'];

$page['head_title'] = $user['name'].' on '.$app['name'];
$page['title'] = '<a href="user.php?id='.$user['id'].'">'.$user['name'].'</a> on <a href="index.php">'.$app['name'].'</a>';

include 'themes/'.$GLOBALS['app']['theme'].'/header.php';

// Show profile

include 'themes/'.$GLOBALS['app']['theme'].'/user_profile.php';

// Show follow button

if ($GLOBALS['app']['friends']['enabled'] == TRUE)
	include 'themes/'.$GLOBALS['app']['theme'].'/friends_button.php';

// Show number of points

if (is_object($GLOBALS['points']))
	include 'themes/'.$GLOBALS['app']['theme'].'/points.php';

// Show new item form

if ($_SESSION['user']['post_permission'] == 1)
	include 'themes/'.$GLOBALS['app']['theme'].'/items_new.php';

// List all items for this user

$items = items_by_user($user['id']);

if (count($items) > 0) {
	
	// Recycle homepage display of articles
	include 'themes/'.$GLOBALS['app']['theme'].'/items_index.php';

} else {
	
	// If own page and no post_permission OR someone else's page show 'no articles yet'
	if (($_SESSION['user']['id'] == $user['id'] && $_SESSION['user']['post_permission'] == 0) || $_SESSION['user']['id'] != $user['id'])
		echo '<p>'.$user['username'].' hasn\'t published any '.$GLOBALS['app']['items']['name_plural'].' yet.</p>';
	
}

// Footer

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>