<?php

require_once 'config/init.php';

$page['selector'] = $_GET['page'];

if ($_GET['user_id'] != $_SESSION['user']['id'])
	exit();

if ($page['selector'] == 'like_add') {
	
	$like_id = $app->like->add($_GET['user_id'], $_GET['item_id']);
	
	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'like', $like_id, 'add');
	
	$item = $app->item->get($_GET['item_id']);
	include 'themes/'.$app->theme.'/likes_list.php';
	
} elseif ($page['selector'] == 'like_remove') {

	$like_id = $app->like->remove($_GET['user_id'], $_GET['item_id']);
	
	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'like', $like_id, 'remove');
	
	$item = $app->item->get($_GET['item_id']);
	include 'themes/'.$app->theme.'/likes_list.php';
	
} elseif ($page['selector'] == 'comment_add') {

	$comment_id = $app->comment->add($_GET['user_id'], $_GET['item_id'], $_GET['content']);
	
	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'comment', $comment_id, 'add', $_GET['content']);

	$item = $app->item->get($_GET['item_id']);
	include 'themes/'.$app->theme.'/comments_list.php';

} elseif ($page['selector'] == 'comment_remove') {
	
	$app->comment->remove($_GET['user_id'], $_GET['item_id'], $_GET['comment_id']);
	
	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'comment', $_GET['comment_id'], 'remove');
	
	$item = $app->item->get($_GET['item_id']);
	include 'themes/'.$app->theme.'/comments_list.php';

} elseif ($page['selector'] == 'friend_add') {

	$friend_id = $app->friend->add($_GET['user_id'], $_GET['friend_user_id']);

	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'friend', $friend_id, 'add');

	if ($app->send_emails == TRUE) {
		// Send 'new follower' email to writer

		$user = $app->user->get($_GET['user_id']);
		$friend = $app->user->get($_GET['friend_user_id']);
		$link = $app->url.'user.php?id='.$_GET['user_id'];

		$to			= "{$friend['username']} <{$friend['email']}>";
		$headers	= "From: David Haywood Smith <davehs@gmail.com>\r\nBcc: davehs@gmail.com\r\nContent-type: text/html\r\n";

		// Load subject and body from template
		include 'themes/'.$app->theme.'/email_follower_new.php';

		// Email user
		mail($to, $subject, $body, $headers);

	}

	$user['id'] = $_GET['friend_user_id'];

	include 'themes/'.$app->theme.'/friends_remove.php';

} elseif ($page['selector'] == 'friend_remove') {

	$friend_id = $app->friend->remove($_GET['user_id'], $_GET['friend_user_id']);

	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'friend', $friend_id, 'remove');

	$user['id'] = $_GET['friend_user_id'];

	include 'themes/'.$app->theme.'/friends_add.php';

}

?>