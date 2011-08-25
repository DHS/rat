<?php

class InvitesController extends Application {
	
	function __construct() {
		
		// Config seems to be empty when accessed in controller so this fails... weird
		
		// Check if feature is disabled or user is logged out
		//if ($this->config->invites['enabled'] == FALSE || empty($_SESSION['user'])) {
		//	
		//	$this->title = 'Page not found';
		//	$this->loadView('partials/header');
		//	$this->loadView('partials/footer');
		//	exit;
		//	
		//}
		
	}
	
	function index() {
		
		$user = User::get_by_id($_SESSION['user']['id']);
		
		$this->invites_remaining = $_SESSION['user']['invites'];
		$this->invites = $user->invites($_SESSION['user']['id']);
		
		if (isset($this->invites_remaining) && $this->invites_remaining == 1) {
			$this->message .= 'You have one invite remaining.<br />';
		} elseif (isset($this->invites_remaining) && $this->invites_remaining > 1) {
			$this->message .= 'You have '.$this->invites_remaining.' invites remaining.<br />';
		} else {
			$this->message .= 'You have no remaining invites.<br />';
		}
		
		$this->title = 'Invites';
		$this->loadLayout('invites/index');
		
	}
	
	function add() {
		
		$_POST['email'] = trim($_POST['email']);

		if ($_POST['email'] == '') {
			$error .= 'Please enter an email address.<br />';
		}

		if ($_SESSION['user']['invites'] < 1) {
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
		if (Invite::check_invited($_SESSION['user']['id'], $_POST['email']) == TRUE) {
			$error .= 'You have already invited this person.<br />';
		}
		
		// Check if already a user
		if (is_object(User::get_by_email($_POST['email'])) == TRUE) {
			$error .= 'This person is already using '.$this->config->name.'!<br />';
		}
		
		if ($error == '') {
			// No problems so do signup + login
			
			// Add invite to database
			$id = Invite::add($_SESSION['user']['id'], $_POST['email']);
			
			// Decrement invites in users table
			User::update_invites($_SESSION['user']['id'], -1);
			
			// Award points
			if (isset($this->plugins->points)) {
				$this->plugins->points->update($_SESSION['user']['id'], $this->plugins->points['per_invite_sent']);
			}
			
			// Log invite
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($_SESSION['user']['id'], 'invite', $id, 'add', $_POST['email']);
			}
			
			if (SITE_IDENTIFIER == 'live') {
				$to		= "{$_POST['username']} <{$_POST['email']}>";
			} else {
				$to		= "{$_POST['username']} <davehs@gmail.com>";
			}
			
			$link = $this->config->url.'signup/'.$id;
			$headers = "From: {$_SESSION['user']['username']} <{$_SESSION['user']['email']}>\r\nBcc: davehs@gmail.com\r\nContent-type: text/html\r\n";
			
			// Load subject and body from template
			$this->loadView('emails/invite_friend');
			
			if ($this->config->send_emails == TRUE) {
				// Email user
				mail($to, $subject, $body, $headers);
			}
			
			$this->message .= 'Invite sent!<br />';
			
		} else {
			
			$_GET['email'] = $_POST['email'];
			
			$this->message .= $error;
			
		}
		
		$this->index();
		
	}
	
}

?>