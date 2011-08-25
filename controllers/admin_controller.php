<?php

class AdminController extends Application {
	
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
		
		// Comment out the following if statment to see admin sectin, $this->config->admin_users not available in constructor
		if (in_array($_SESSION['user']['id'], $this->config->admin_users) != TRUE) {
			// User not an admin
			
			$this->title = 'Page not found';
			$this->loadView('partials/header');
			$this->loadView('partials/footer');
			exit;
			
		}
		
	}
	
	// Show admin dashboard
	function index() {
		
		$this->title = 'Admin &raquo; Dashboard';
		$this->users = Admin::list_users();
		$this->page['users_beta'] = Admin::list_users_beta();
		$this->loadLayout('admin/index', 'admin');
		
	}
	
	// Setup your rat installation
	function setup() {
		
		$this->title = 'Setup';
		
		if (isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
			// Do setup
			
			$user_id = User::add($_POST['email']);
			User::signup($user_id, $_POST['username'], $_POST['password'], $this->config->encryption_salt);
			
			$user = User::get_by_email($_POST['email']);
			
			foreach ($user as $key => $value) {
				$user_array[$key] = $value;
			}
			$_SESSION['user'] = $user_array;
			
			// Log login
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'signup');
			}
			
			$this->message = 'Rat is now setup and you are logged in!';
			
			// Go forth!
			if (SITE_IDENTIFIER == 'live') {
				header('Location: '.$this->config->url.'?message='.urlencode($this->message));
			} else {
				header('Location: '.$this->config->dev_url.'?message='.urlencode($this->message));
			}
			
			exit();
			
		} else {
			// Show setup form
			
			$this->message = 'Welcome to Rat! Please enter your details:';
			$this->loadLayout('admin/setup');

		}
		
	}
	
	// Show list of beta signups
	function signups() {
		
		$this->title = 'Admin &raquo; Beta signups';	
		$this->users = Admin::list_users_beta();
		$this->loadLayout('admin/signups', 'admin');
		
	}
	
	// Show list of users
	function users() {
		
		$this->title = 'Admin &raquo; Users';
		$this->users = Admin::list_users();
		$this->loadLayout('admin/users', 'admin');
		
	}
	
	// Show most recent entries in the log (not named log to avoid conflict with native PHP function)
	function history() {
		
		if (isset($this->plugins->log)) {
			
			$this->title = 'Admin &raquo; Dashboard';
			$this->loadPartial('header');
			$this->loadView('admin/menu');
			$this->plugins->log->view();
			$this->loadPartial('footer');
			
		}
		
	}
	
	// Grant access to a beta signup
	function invite() {
		
		$email = $_POST['email'];
		
		if ($email != '') {
			
			// Add invite to database
			$id = Invite::add($_SESSION['user']['id'], $email);
			
			// Log invite
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($_SESSION['user']['id'], 'invite', $id, 'admin_add', $email);
			}
			
			if (SITE_IDENTIFIER == 'live') {
				$to		= "{$_POST['username']} <{$email}>";
			} else {
				$to		= "{$_POST['username']} <davehs@gmail.com>";
			}
			
			$link		= $this->config->url.'signup.php?code='.$id.'&email='.urlencode($email);
			$headers	= "From: {$_SESSION['user']['username']} <{$_SESSION['user']['email']}>\r\nContent-type: text/html\r\n";
			
			// Load template into $body variable
			$this->loadView('email/invite_admin');
			
			if ($this->config->send_emails == TRUE) {
				// Email user
				mail($to, $subject, $body, $headers);
			}
			
			$this->message = 'User invited!';
			
		}

		$this->signups();
		
	}
	
	function grant_invites() {
		
		if ($_GET['count'] > 0) {
			
			Admin::update_invites($_GET['count']);
			
			$this->message = 'Invites updated!';
			
		}
		
		$this->users();
		
	}
	
}

?>