<?php

class AdminController extends Application {
	
	protected $requireAdmin = array('index', 'signups', 'users', 'history', 'invite', 'grant_invites');
	
	// TODO: Move this to a filter
	function __construct() {
		
		// If user is admin or first user then let them pass otherwise exit
		
		if ($this->uri['action'] == 'setup') {
			// Setup page called so make sure user count = 0
			
			if (count(Admin::list_users()) != 0) {
				
				$this->title = 'Page not found';
				$this->loadView('partials/header');
				$this->loadView('partials/footer');
				exit;
				
			}
			
		}
		/*
		// Comment out the following if statment to see admin sectin, $this->config->admin_users not available in constructor
		if (in_array($_SESSION['user_id'], $this->config->admin_users) != TRUE) {
			// User not an admin
			
			$this->title = 'Page not found';
			$this->loadView('partials/header');
			$this->loadView('partials/footer');
			exit;
			
		}
		*/
	}
	
	// Show admin dashboard
	function index() {
		
		$this->title = 'Admin &raquo; Dashboard';
		$this->users = Admin::list_users();
		$this->page['users_beta'] = Admin::list_users_beta();
		$this->loadView('admin/index', 'admin');
		
	}
	
	// Setup your rat installation
	function setup() {
		
		$this->title = 'Setup';
		
		if (isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
			// Do setup
			
			$user_id = User::add($_POST['email']);
			User::signup($user_id, $_POST['username'], $_POST['password'], $this->config->encryption_salt);
			
			$user = User::get_by_email($_POST['email']);
			
			// Update session
			$_SESSION['user_id'] = $user->id;
			
			// Log login
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($_SESSION['user_id'], 'user', NULL, 'signup');
			}
			
			Application::flash('success', 'You are now logged in to your app!');
			
			// Go forth!
			if (SITE_IDENTIFIER == 'live') {
				header('Location: '.$this->url_for('items', 'add'));
			} else {
				header('Location: '.$this->url_for('items', 'add'));
			}
			
			exit();
			
		} else {
			// Show setup form
			
			Application::flash('info', 'Welcome to Rat! Please enter your details:');
			$this->loadView('admin/setup');
			
		}
		
	}
	
	// Show list of beta signups
	function signups() {
		
		$this->title = 'Admin &raquo; Beta signups';	
		$this->users = Admin::list_users_beta();
		$this->loadView('admin/signups', 'admin');
		
	}
	
	// Show list of users
	function users() {
		
		$this->title = 'Admin &raquo; Users';
		$this->users = Admin::list_users();
		$this->loadView('admin/users', 'admin');
		
	}
	
	// Show most recent entries in the log (not named log to avoid conflict with native PHP function)
	function history() {
		
		if (isset($this->plugins->log)) {
			
			$this->title = 'Admin &raquo; Log';
			$this->loadPartial('header');
			$this->loadPartial('admin_menu');
			$this->plugins->log->view();
			$this->loadPartial('footer');
			
		}
		
	}
	
	// Grant access to a beta signup
	function invite() {
		
		$user = User::get_by_id($_SESSION['user_id']);
		$email = $_POST['email'];
		
		if ($email != '') {
			
			// Add invite to database
			$id = Invite::add($_SESSION['user_id'], $email);
			
			// Log invite
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($_SESSION['user_id'], 'invite', $id, 'admin_add', $email);
			}
			
			if (SITE_IDENTIFIER == 'live') {
				$to		= "{$_POST['username']} <{$email}>";
			} else {
				$to		= "{$_POST['username']} <davehs@gmail.com>";
			}
			
			$link		= $this->config->url.'signup.php?code='.$id.'&email='.urlencode($email);
			$headers	= "From: {$user->username} <{$user->email}>\r\nContent-type: text/html\r\n";
			
			// Load template into $body variable
			include "themes/{$this->config->theme}/emails/invite_admin.php";
			
			if ($this->config->send_emails == TRUE) {
				// Email user
				mail($to, $subject, $body, $headers);
			}
			
			Application::flash('success', 'User invited!');
			
		}

		$this->signups();
		
	}
	
	function grant_invites() {
		
		if ($this->uri['params']['count'] > 0) {
			
			Admin::update_invites($this->uri['params']['count']);
			
			Application::flash('success', 'Invites updated!');
			
		}
		
		$this->users();
		
	}
	
}

?>
