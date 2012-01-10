<?php

class AdminController extends Application {
	
	protected $requireAdmin = array('index', 'signups', 'users', 'history', 'invite', 'grant_invites');
	
	// Show admin dashboard
	function index() {
		
		$this->title = 'Admin &raquo; Dashboard';
		$this->users = Admin::list_users();
		$this->users_beta = Admin::list_users_beta();
		$this->loadView('admin/index', NULL, 'admin');
		
	}

	// Setup your rat installation
	function setup() {
		
		$this->title = 'Setup';
		
		if (! Admin::tables_exist() && isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
			// Do setup
			
			// Write config!
			$this->create_tables();

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

			if (! Admin::tables_exist()) {
				Application::flash('info', 'Welcome to Rat!');
				$this->loadView('admin/setup');
			} else {
				throw new RoutingException($this->uri, "Page not found");
			}
			
		}
		
	}
	
	// Show list of beta signups
	function signups() {
		
		$this->title = 'Admin &raquo; Beta signups';	
		$this->users = Admin::list_users_beta();
		$this->loadView('admin/signups', NULL, 'admin');
		
	}
	
	// Show list of users
	function users() {
		
		$this->title = 'Admin &raquo; Users';
		$this->users = Admin::list_users();
		$this->loadView('admin/users', NULL, 'admin');
		
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
			
			$to			= "{$user->username} <{$email}>";
			$link		= $this->config->url.'users/add/'.$id.'/?email='.urlencode($email);
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
