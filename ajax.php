<?php

require_once 'config/init.php';

$page['selector'] = $_GET['page'];

if ($_GET['user_id'] != $_SESSION['user']['id'])
	exit();

if ($page['selector'] == 'like_add') {
	
	$like_id = $like->add($_GET['user_id'], $_GET['item_id']);
	
	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'like', $like_id, 'add');
	
	$item = $item->get($_GET['item_id']);
	include 'themes/'.$GLOBALS['app']['theme'].'/likes_list.php';
	
} elseif ($page['selector'] == 'like_remove') {

	$like_id = $like->remove($_GET['user_id'], $_GET['item_id']);
	
	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'like', $like_id, 'remove');
	
	$item = $item->get($_GET['item_id']);
	include 'themes/'.$GLOBALS['app']['theme'].'/likes_list.php';
	
} elseif ($page['selector'] == 'comment_add') {

	$comment_id = $comment->get($_GET['user_id'], $_GET['item_id'], $_GET['content']);
	
	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'comment', $comment_id, 'add', $_GET['content']);

	$item = $item->get($_GET['item_id']);
	include 'themes/'.$GLOBALS['app']['theme'].'/comments_list.php';

} elseif ($page['selector'] == 'comment_remove') {
	
	$comment->remove($_GET['user_id'], $_GET['item_id'], $_GET['comment_id']);
	
	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'comment', $_GET['comment_id'], 'remove');
	
	$item = $item->get($_GET['item_id']);
	include 'themes/'.$GLOBALS['app']['theme'].'/comments_list.php';

} elseif ($page['selector'] == 'friend_add') {

	$friend_id = friends_add($_GET['user_id'], $_GET['friend_user_id']);

	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'friend', $friend_id, 'add');

	if ($GLOBALS['app']['send_emails'] == TRUE) {
		// Send 'new follower' email to writer

		$user = $user->get($_GET['user_id']);
		$friend = $user->get($_GET['friend_user_id']);
		$link = $GLOBALS['app']['url'].'user.php?id='.$_GET['user_id'];

		$to			= "{$friend['username']} <{$friend['email']}>";
		$subject	= "[{$GLOBALS['app']['name']}] {$user['username']} is now following you on {$GLOBALS['app']['name']}!";
		// Load template into $body variable
		include 'themes/'.$GLOBALS['app']['theme'].'/email_follower.php';
		$headers	= "From: David Haywood Smith <davehs@gmail.com>\r\nBcc: davehs@gmail.com\r\nContent-type: text/html\r\n";

		// Email user
		mail($to, $subject, $body, $headers);

	}

	$user['id'] = $_GET['friend_user_id'];

	include 'themes/'.$GLOBALS['app']['theme'].'/friends_remove.php';

} elseif ($page['selector'] == 'friend_remove') {

	$friend_id = friends_remove($_GET['user_id'], $_GET['friend_user_id']);

	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_GET['user_id'], 'friend', $friend_id, 'remove');

	$user['id'] = $_GET['friend_user_id'];

	include 'themes/'.$GLOBALS['app']['theme'].'/friends_add.php';

}

?>