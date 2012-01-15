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
		
		$session['user_id'] = $_SESSION['user_id'];
		$friends = TRUE;
		
		$this->user = $friend;
		
		if ($this->config->theme == 'twig') {
			echo $this->twig->render("partials/friend.html", array('app' => $this, 'session' => $session, 'user' => $friend, 'friends' => $friends));
		} else {
			$this->loadPartial('friend');
		}
		
	}
	
	function remove($friend_id) {
		
		$user = User::get_by_id($_SESSION['user_id']);
		$friend = User::get_by_id($friend_id);
		
		// Check that frienship is legit
		if ($friend->friend_check($_SESSION['user_id']) == TRUE) {
			
			// Remove friendship
			$user->friend_remove($friend_id);
			
			// Log parting of ways
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($user->id, 'friend', $friend_id, 'remove');
			}
			
		}
		
		$session['user_id'] = $_SESSION['user_id'];
		$friends = FALSE;
		
		$this->user = $friend;
		
		if ($this->config->theme == 'twig') {
			echo $this->twig->render("partials/friend.html", array('app' => $this, 'session' => $session, 'user' => $friend, 'friends' => $friends));
		} else {
			$this->loadPartial('friend');
		}
		
	}
	
}

?>