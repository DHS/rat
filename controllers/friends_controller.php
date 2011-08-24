<?php

class FriendsController {
	
	function index() {
		
		global $app;
		
		// Not needed?
		
	}
	
	function add($friend_id) {
		
		global $app;
		
		$friendship_id = Friend::add($_SESSION['user']['id'], $friend_id);
		
		if (isset($app->plugins->log))
			$app->plugins->log->add($_SESSION['user']['id'], 'friend', $friendship_id, 'add');
		
		if ($app->config->send_emails == TRUE) {
			// Send 'new follower' email to writer
			
			$user = User::get($_SESSION['user']['id']);
			$friend = User::get($friend_id);
			$link = $app->config->url.'users/show/'.$_SESSION['user']['id'];
			
			$to			= "{$friend['username']} <{$friend['email']}>";
			$headers	= "From: David Haywood Smith <davehs@gmail.com>\r\nBcc: davehs@gmail.com\r\nContent-type: text/html\r\n";
			
			// Load subject and body from template
			$app->loadView('email/follower_new');
			
			// Email user
			mail($to, $subject, $body, $headers);
			
		}
		
		$app->page->user['id'] = $friend_id;
		
		$app->loadView('friends/remove');
		
	}
	
	function remove($friend_id) {
		
		global $app;
		
		$friend_id = Friend::remove($_SESSION['user']['id'], $friend_id);
		
		if (isset($app->plugins->log))
			$app->plugins->log->add($_SESSION['user']['id'], 'friend', $friend_id, 'remove');
		
		$app->page->user['id'] = $friend_id;
		
		$app->loadView('friends/add');
		
	}
	
}


?>