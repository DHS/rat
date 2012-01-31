<?php

class AdminController extends Application {
	
	protected $requireAdmin = array('index', 'signups', 'users', 'history', 'invite', 'grant_invites');
	
	// Show admin dashboard
	function index() {
		
		$users = Admin::list_users();
		$users_beta = Admin::list_users_beta();
		
		// old templates
		$this->title = 'Admin - Dashboard';
		$this->users = $users;
		$this->users_beta = $users_beta;
		
		$this->loadView('admin/index', array('users' => $users, 'users_beta' => $users_beta), 'admin');
		
	}
	
	// Show app spec
	function spec() {
		
		$conf = get_object_vars($this->config);
		
		if ($_POST) {
			
			// Pull post vars into $conf array
			
			foreach ($_POST as $key => $value) {
				$conf[$key] = $value;
			}
			
			//// Overwrite checkbox fields
			//$checkboxes = array('beta', 'private', 'items\'][\'titles\'][\'enabled', 'items["content"]["enabled"]', 'items["uploads"]["enabled"]', 'items["comments"]["enabled"]', 'items["likes"]["enabled"]');
			//
			//foreach ($checkboxes as $key => $checkbox) {
			//	if ($_POST[$checkbox] == 'on') {
			//		$conf[$checkbox] = 'TRUE';
			//	} else {
			//		$conf[$checkbox] = 'FALSE';
			//	}
			//}
			
			if ($_POST['beta'] == 'on') {
				$conf['beta'] = 'TRUE';
			} else {
				$conf['beta'] = 'FALSE';
			}
			
			if ($_POST['private'] == 'on') {
				$conf['private'] = 'TRUE';
			} else {
				$conf['private'] = 'FALSE';
			}
			
			if ($_POST['items']['titles']['enabled'] == 'on') {
				$conf['items']['titles']['enabled'] = 'TRUE';
			} else {
				$conf['items']['titles']['enabled'] = 'FALSE';
			}
			
			if ($_POST['items']['content']['enabled'] == 'on') {
				$conf['items']['content']['enabled'] = 'TRUE';
			} else {
				$conf['items']['content']['enabled'] = 'FALSE';
			}
			
			if ($_POST['items']['uploads']['enabled'] == 'on') {
				$conf['items']['uploads']['enabled'] = 'TRUE';
			} else {
				$conf['items']['uploads']['enabled'] = 'FALSE';
			}
			
			if ($_POST['items']['comments']['enabled'] == 'on') {
				$conf['items']['comments']['enabled'] = 'TRUE';
			} else {
				$conf['items']['comments']['enabled'] = 'FALSE';
			}
			
			if ($_POST['items']['likes']['enabled'] == 'on') {
				$conf['items']['likes']['enabled'] = 'TRUE';
			} else {
				$conf['items']['likes']['enabled'] = 'FALSE';
			}
			
			if ($_POST['invites']['enabled'] == 'on') {
				$conf['invites']['enabled'] = 'TRUE';
			} else {
				$conf['invites']['enabled'] = 'FALSE';
			}
			
			if ($_POST['friends']['enabled'] == 'on') {
				$conf['friends']['enabled'] = 'TRUE';
			} else {
				$conf['friends']['enabled'] = 'FALSE';
			}
			
			if ($_POST['friends']['asymmetric'] == 'on') {
				$conf['friends']['asymmetric'] = 'TRUE';
			} else {
				$conf['friends']['asymmetric'] = 'FALSE';
			}
			
			$this->writeConfig('application', $conf);
			Application::flash('success', 'Success! <a href="'.$this->url_for('admin', 'spec').'">Click here</a> to reload with your new config!');
			
		}
		
		$this->title = 'Admin - Config';
		$this->loadView('admin/spec', NULL, 'admin');
		
	}
	
	// Show list of beta signups
	function signups() {
		
		$users = Admin::list_users_beta();
		
		// old template
		$this->title = 'Admin - Beta signups';
		$this->users = $users;
		
		$this->loadView('admin/signups', array('users' => $users), 'admin');
		
	}
	
	// Show list of users
	function users() {
		
		$users = Admin::list_users();
		
		// old template
		$this->title = 'Admin - Users';
		$this->users = $users;
		
		$this->loadView('admin/users', array('users' => $users), 'admin');
		
	}
	
	// Show most recent entries in the log (not named log to avoid conflict with native PHP function)
	function history() {
		
		if (isset($this->plugins->log)) {
			
			$this->title = 'Admin - Log';
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
			if ($this->config->theme == 'twig') {
				header('Location: '.$this->url_for('admin', 'spec'));
			} else {
				// old template
				header('Location: '.$this->url_for('item', 'add'));
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
			
			$to			= $email;
			$link		= $this->config->url.'users/add/'.$id.'/?email='.urlencode($email);
			$headers	= "From: {$user->username} <{$user->email}>\r\nContent-type: text/html\r\n";
			
			// Load template into $body variable
			// old template
			include "themes/{$this->config->theme}/emails/invite_admin.php";
			
			if ($this->config->theme == 'twig') {
				
				$twig = new Twig_Environment(new Twig_Loader_String(), array('auto_reload' => TRUE));
				
				$to			= array('email' => $email);
				$subject	= '['.$this->config->name.'] Your '.$this->config->name.' invite is here!';
				$body		= $twig->render(file_get_contents("themes/{$this->config->theme}/emails/admin_invite.html"), array('app' => array('config' => $settings)));
				
			}
			
			// Email user
			send_email($to, $subject, $body, $headers);
			
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