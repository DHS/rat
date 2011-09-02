<?php

class InvitesController extends Application {
	
	protected $requireLoggedIn = array('index', 'add');
	protected $requireInvitesEnabled = array('index', 'add');
	
	function index() {
		
		$user = User::get_by_id($_SESSION['user_id']);
		
		$this->invites_remaining = $user->invites;
		$this->invites = $user->invites();
		
		if (isset($this->invites_remaining) && $this->invites_remaining == 1) {
			Application::flash('info', 'You have one invite remaining.');
		} elseif (isset($this->invites_remaining) && $this->invites_remaining > 1) {
			Application::flash('info', 'You have '.$this->invites_remaining.' invites remaining.');
		} else {
			Application::flash('info', 'You have no remaining invites.');
		}
		
		$this->title = 'Invites';

		if ($this->json) {
			$this->render_json($this->invites);
		} else {
			$this->loadView('invites/index');
		}
	
	}
	
	function add() {
		
		$user = User::get_by_id($_SESSION['user_id']);
		
		$_POST['email'] = trim($_POST['email']);

		if ($_POST['email'] == '') {
			$error .= 'Please enter an email address.<br />';
		}

		if ($user->invites < 1) {
			$error .= 'You don\'t have any invites remaining.<br />';
		}
		
		// Check if email contains spaces
		if (User::check_contains_spaces($_POST['email']) == TRUE) {
			$error .= 'Email address cannot contain spaces.<br />';
		}
		
		if (User::check_contains_at($_POST['email']) != TRUE) {
			$error .= 'Email must contain an @ symbol.<br />';
		}
		
		// Check if already invited
		if (Invite::check_invited($_SESSION['user_id'], $_POST['email']) == TRUE) {
			$error .= 'You have already invited this person.<br />';
		}
		
		// Check if already a user
		if (is_object(User::get_by_email($_POST['email'])) == TRUE) {
			$error .= 'This person is already using '.$this->config->name.'!<br />';
		}
		
		if ($error == '') {
			// No problems so do signup + login
			
			// Add invite to database
			$id = Invite::add($_SESSION['user_id'], $_POST['email']);
			
			// Decrement invites in users table
			$user->update_invites(-1);
			
			// Award points
			if (isset($this->plugins->points)) {
				$this->plugins->points->update($_SESSION['user_id'], $this->plugins->points['per_invite_sent']);
			}
			
			// Log invite
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($_SESSION['user_id'], 'invite', $id, 'add', $_POST['email']);
			}
			
			if (SITE_IDENTIFIER == 'live') {
				$to		= "{$_POST['username']} <{$_POST['email']}>";
			} else {
				$to		= "{$_POST['username']} <davehs@gmail.com>";
			}
			
			$link = $this->config->url.'signup/'.$id;
			$headers = "From: {$user->username} <{$user->email}>\r\nBcc: davehs@gmail.com\r\nContent-type: text/html\r\n";
			
			// Load subject and body from template
			include "themes/{$this->config->theme}/emails/invite_friend.php";
			
			if ($this->config->send_emails == TRUE) {
				// Email user
				mail($to, $subject, $body, $headers);
			}
			
			Application::flash('success', 'Invite sent!');
			
		} else {
			
			$this->uri['params']['email'] = $_POST['email'];
			
			Application::flash('error', $error);
			
		}
		
		$this->index();
		
	}
	
}

?>
