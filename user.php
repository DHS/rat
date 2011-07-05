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

if ($user['full_name'] != NULL) {
	// Full name set so use that for page title
	$page['title'] = $user['full_name'].' on '.$app['name'];
	$app['page_title'] = '<a href="user.php?id='.$user['id'].'">'.$user['full_name'].'</a> on <a href="index.php">'.$app['name'].'</a>';
} else {
	// Full name not set so use username for page title
	$page['title'] = $user['username'].' on '.$app['name'];
	$app['page_title'] = '<a href="user.php?id='.$user['id'].'">'.$user['username'].'</a> on <a href="index.php">'.$app['name'].'</a>';
}

include 'themes/'.$GLOBALS['app']['theme'].'/header.php';

// Show profile

include 'themes/'.$GLOBALS['app']['theme'].'/user_profile.php';

// Show add friend button

//  What does this do?
//  	
//  	Format: "Follow string - Follow button"
//
//  	If (followers) string
//  	
//  	If (not current user) button
//  	If (not loggedin) disguised login button
//
//  	If (both) dot separator
//  	

$followers = count(friends_get_followers($user['id']));

if (empty($_SESSION['user'])) {
	
	include 'themes/'.$GLOBALS['app']['theme'].'/friends_add_loggedout.php';
	
} else {
	
	// If current user & no followers then don't include holder (as it would be empty)
	$show_holder = TRUE;
	if ($user['id'] == $_SESSION['user']['id'] && $followers == 0)
		$show_holder = FALSE;
	
	if ($show_holder == TRUE)
		include 'themes/'.$GLOBALS['app']['theme'].'/friends_button_holder.php';
	
}

// Show number of points

if (is_object($GLOBALS['points']))
	include 'themes/'.$GLOBALS['app']['theme'].'/points.php';

// Show new item form

if ($_SESSION['user']['post_permission'] == 1)
	include 'themes/'.$GLOBALS['app']['theme'].'/items_new.php';

// List all items for this user

$items = items_by_user($user['id'], 1, NULL);

if (count($items) > 0) {
	
	//echo '<h2>'.ucfirst($GLOBALS['app']['items']['name_plural']).' by '.$user['username'].'</h2>';
	//include 'themes/'.$GLOBALS['app']['theme'].'/items_user.php';

	// cheeky hack to hide avatar on each post in items_index.php
	unset($GLOBALS['gravatar']);
	
	// recycle homepage display of articles
	include 'themes/'.$GLOBALS['app']['theme'].'/items_index.php';

} else {
	
	// if own page and no post_permission OR someone else's page show 'no articles yet'
	if (($_SESSION['user']['id'] == $user['id'] && $_SESSION['user']['post_permission'] == 0) || $_SESSION['user']['id'] != $user['id'])
		echo '<p>'.$user['username'].' hasn\'t published any '.$GLOBALS['app']['items']['name_plural'].' yet.</p>';
	
}

// Footer

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>