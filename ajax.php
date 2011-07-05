<?php

require_once 'config/init.php';

$page['selector'] = $_GET['page'];

if ($_GET['user_id'] != $_SESSION['user']['id'])
	exit();

if ($page['selector'] == 'like_add') {
	
	$like_id = likes_add($_GET['user_id'], $_GET['item_id']);
	
	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'like', $like_id, 'add');
	
	$item = items_get_by_id($_GET['item_id']);
	include 'themes/'.$GLOBALS['app']['theme'].'/likes_show.php';
	
} elseif ($page['selector'] == 'like_remove') {

	$like_id = likes_remove($_GET['user_id'], $_GET['item_id']);
	
	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'like', $like_id, 'remove');
	
	$item = items_get_by_id($_GET['item_id']);
	include 'themes/'.$GLOBALS['app']['theme'].'/likes_show.php';
	
} elseif ($page['selector'] == 'comment_add') {

	$comment_id = comments_add($_GET['user_id'], $_GET['item_id'], $_GET['content']);
	
	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'comment', $comment_id, 'add', $_GET['content']);

	$item = items_get_by_id($_GET['item_id']);
	include 'themes/'.$GLOBALS['app']['theme'].'/comments_show.php';

} elseif ($page['selector'] == 'comment_remove') {
	
	comments_remove($_GET['user_id'], $_GET['item_id'], $_GET['comment_id']);
	
	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'comment', $_GET['comment_id'], 'remove');
	
	$item = items_get_by_id($_GET['item_id']);
	include 'themes/'.$GLOBALS['app']['theme'].'/comments_show.php';

} elseif ($page['selector'] == 'friend_add') {

	$friend_id = friends_add($_GET['user_id'], $_GET['friend_user_id']);

	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'friend', $friend_id, 'add');

	if ($GLOBALS['app']['send_emails'] == TRUE) {
		// Send 'new follower' email to writer

		$user = user_get_by_id($_GET['user_id']);
		$friend = user_get_by_id($_GET['friend_user_id']);
		$link = $GLOBALS['app']['url'].'user.php?id='.$_GET['user_id'];

		$to			= "{$friend['username']} <{$friend['email']}>";
		$subject	= "[{$GLOBALS['app']['name']}] {$user['username']} is now following you on {$GLOBALS['app']['name']}!";
		$body		= "Hi {$friend['username']},\n\nJust to let you know that you have a new follower on {$GLOBALS['app']['name']}:\n\n$link\n\nYou should publish another {$GLOBALS['app']['items']['name']} to celebrate!\n\nBest regards,\n\nDavid Haywood Smith, creator of {$GLOBALS['app']['name']}";
		$headers	= "From: David Haywood Smith <davehs@gmail.com>\r\nBcc: davehs@gmail.com\r\n";

		// Email user
		mail($to, $subject, $body, $headers);

	}

	$user['id'] = $_GET['friend_user_id'];
	$followers = count(friends_get_followers($_GET['friend_user_id']));
	if ($followers == 1) {
		$string = '<strong>One</strong> pioneering follower &middot; ';
	} elseif ($followers > 1) {
		$string = '<strong>'.$followers.'</strong> followers &middot; ';
	}
	// Shouldn't really be echoing in a controller
	echo $string;
	include 'themes/'.$GLOBALS['app']['theme'].'/friends_remove.php';

} elseif ($page['selector'] == 'friend_remove') {

	$friend_id = friends_remove($_GET['user_id'], $_GET['friend_user_id']);

	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'friend', $friend_id, 'remove');

	$user['id'] = $_GET['friend_user_id'];
	$followers = count(friends_get_followers($_GET['friend_user_id']));
	if ($followers == 1) {
		$string = '<strong>One</strong> pioneering follower &middot; ';
	} elseif ($followers > 1) {
		$string = '<strong>'.$followers.'</strong> followers &middot; ';
	}
	// Shouldn't really be echoing in a controller
	echo $string;
	include 'themes/'.$GLOBALS['app']['theme'].'/friends_add.php';

}

?>