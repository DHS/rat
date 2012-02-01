<?php

class InvitesController extends Application {
	
	protected $requireLoggedIn = array('index', 'add');
	protected $requireInvitesEnabled = array('index', 'add');
	
	function index() {
		
		$user = User::get_by_id($_SESSION['user_id']);
		
		$invites_remaining = $user->invites;
		$invites_sent = $user->invites();
		
		if (isset($invites_remaining) && $invites_remaining == 1) {
			$message = 'You have one invite remaining.';
		} elseif (isset($invites_remaining) && $invites_remaining > 1) {
			$message = 'You have '.$invites_remaining.' invites remaining.';
		} else {
			$message = 'You have no remaining invites.';
		}
		
		// old template
		$this->title = 'Invites';
		$this->invites = $invites_sent;
		$this->invites_remaining = $invites_remaining;
		
		if ($this->json) {
			$this->render_json($this->invites);
		} else {
			$this->loadView('invites/index', array('message' => $message, 'invites_sent' => $invites_sent, 'invites_remaining' => $invites_remaining));
		}
	
	}
	
	function add() {
		
		$user = User::get_by_id($_SESSION['user_id']);
		
		$_POST['email'] = trim($_POST['email']);
		
		$error = '';
		
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
			
			$admin = User::get_by_id($this->config->admin_users[0]);
			
			$to			= "{$_POST['email']}";
			$link		= $this->config->url.'signup/'.$id;
			
			// Load subject and body from template
			// old template
			include "themes/{$this->config->theme}/emails/invite_friend.php";
			
			if ($this->config->theme == 'twig') {
				
				$to			= array('email' => $_POST['email']);
				$subject	= '['.$this->config->name.'] An invitation from '.$user->username;
				$body		= $this->twig_string->render(file_get_contents("themes/{$this->config->theme}/emails/invite_friend.html"), array('link' => $link, 'user' => $user, 'app' => array('config' => $this->config)));
				
			}
			
			// Email user
			$this->email->send_email($to, $subject, $body);
			
			Application::flash('success', 'Invite sent!');
			
		} else {
			
			$this->uri['params']['email'] = $_POST['email'];
			
			Application::flash('error', $error);
			
		}
		
		$this->index();
		
	}
	
}

?>