<?php

class FriendsController extends Application {
	
	function index() {
		
		// Not needed?
		
	}
	
	function add($friend_id) {
		
		$friendship_id = Friend::add($_SESSION['user']['id'], $friend_id);
		
		if (isset($this->plugins->log))
			$this->plugins->log->add($_SESSION['user']['id'], 'friend', $friendship_id, 'add');
		
		if ($this->config->send_emails == TRUE) {
			// Send 'new follower' email to writer
			
			$user = User::get($_SESSION['user']['id']);
			$friend = User::get($friend_id);
			$link = $this->config->url.'users/show/'.$_SESSION['user']['id'];
			
			$to			= "{$friend['username']} <{$friend['email']}>";
			$headers	= "From: David Haywood Smith <davehs@gmail.com>\r\nBcc: davehs@gmail.com\r\nContent-type: text/html\r\n";
			
			// Load subject and body from template
			$this->loadView('email/follower_new');
			
			// Email user
			mail($to, $subject, $body, $headers);
			
		}
		
		$page['user']['id'] = $friend_id;
		
		$this->loadView('friends/remove');
		
	}
	
	function remove($friend_id) {
		
		$friend_id = Friend::remove($_SESSION['user']['id'], $friend_id);
		
		if (isset($this->plugins->log))
			$this->plugins->log->add($_SESSION['user']['id'], 'friend', $friend_id, 'remove');
		
		$page['user']['id'] = $friend_id;
		
		$this->loadView('friends/add');
		
	}
	
}


?>