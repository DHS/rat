<?php

class FriendsController extends Application {
	
	protected $requireLoggedIn = array('add', 'remove');

	function add($friend_id) {
		
		$user = User::get_by_id($_SESSION['user_id']);
		$friend = User::get_by_id($friend_id);
		
		$user->friend_add($friend_id);
		
		if (isset($this->plugins->log)) {
			$this->plugins->log->add($user->id, 'friend', $friend_id, 'add');
		}
		
		if ($this->config->send_emails == TRUE) {
			// Send 'new follower' email to writer
			
			$admin = User::get_by_id($this->config->admin_users[0]);
			
			$to			= "{$friend['username']} <{$friend['email']}>";
			$link		= $this->config->url.'users/show/'.$user->id;
			$headers	= "From: {$admin->username} <{$admin->email}>\r\nBcc: {$admin->email}\r\nContent-type: text/html\r\n";
			
			// Load subject and body from template
			include "themes/{$this->config->theme}/emails/follower_new.php";
			
			// Email user
			mail($to, $subject, $body, $headers);
			
		}
		
		$this->user = $friend;
		$this->loadPartial('friend');
		
	}
	
	function remove($friend_id) {
		
		$user = User::get_by_id($_SESSION['user_id']);
		$friend = User::get_by_id($friend_id);
		
		// Check that frienship is legit
		if ($user->friend_check($friend_id) == TRUE) {
			
			// Remove friendship
			$user->friend_remove($friend_id);
			
			// Log parting of ways
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($user->id, 'friend', $friend_id, 'remove');
			}
			
		}
		
		$this->user = $friend;
		$this->loadPartial('friend');
		
	}
	
}

?>