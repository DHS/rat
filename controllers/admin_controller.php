<?php

class AdminController extends Application {
	
	protected $requireAdmin = array('index', 'signups', 'users', 'history', 'invite', 'grant_invites');
	
	// Show admin dashboard
	function index() {
		
		$users = Admin::list_users();
		$users_beta = Admin::list_users_beta();
		
		// old templates
		$this->title = 'Admin &raquo; Dashboard';
		$this->users = $users;
		$this->users_beta = $users_beta;
		
		$this->loadView('admin/index', array('users' => $users, 'users_beta' => $users_beta), 'admin');
		
	}
	
	// Show app spec
	function spec() {
		
		$conf = get_object_vars($this->config);
		
		if ($_POST) {
			foreach ($_POST as $key => $value) {
				$conf[$key] = $value;
			}
			$this->writeConfig('application', $conf);
		}
		
		$this->title = 'Admin &raquo; Config';
		$this->loadView('admin/spec', NULL, 'admin');
		
	}
	
	// Show list of beta signups
	function signups() {
		
		$users = Admin::list_users_beta();
		
		// old template
		$this->title = 'Admin &raquo; Beta signups';
		$this->users = $users;
		
		$this->loadView('admin/signups', array('users' => $users), 'admin');
		
	}
	
	// Show list of users
	function users() {
		
		$users = Admin::list_users();
		
		// old template
		$this->title = 'Admin &raquo; Users';
		$this->users = $users;
		
		$this->loadView('admin/users', array('users' => $users), 'admin');
		
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
	
	// Setup your rat installation
	function setup() {
		
		$this->title = 'Setup';
		
		if (count(Admin::list_users()) == 0 && isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
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

			if (count(Admin::list_users()) != 0) {
				Application::flash('info', 'Welcome to Rat!');
				$this->loadView('admin/setup');
			} else {
				throw new RoutingException($this->uri, "Page not found");
			}
			
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
