<?php

class UsersController extends Application {
	
	function __construct() {
		
		// Check if user is logged in and trying to signup
		if ($this->uri['action'] == 'add' && !empty($_SESSION['user'])) {

			$this->title = 'Signup';
			$this->message = 'You are already logged in!';
			$this->loadView('partials/header');
			$this->loadView('partials/footer');
			exit;

		}
		
	}
	
	// Show a list of users
	function index() {
		
		// Not needed?
		
	}
	
	// Add a user
	function add($code) {
		
		if ($_POST['email'] != '') {
			
			if ($_POST['code'] != '') {
				
				$this->do_signup('code');
				
			} else {
				
				if ($this->config->beta == TRUE) {
					
					$this->do_signup('beta');
					
				} else {
					
					$this->do_signup('full');
					
				}
				
			}
			
		} else {
			
			// Show signup form
			
			if ($this->config->beta == TRUE) {
				// Show beta signup form
				$this->loadLayout('users/add_beta');
			} else {
				// Show full signup form
				
				if (isset($code)) {
					$this->page['code'] = $code;
				}
				
				$this->loadLayout('users/add');
				
			}
			
		}
		
	}
	
	// Show a user
	function show($id) {
		
		$this->user = User::get_by_id($id);
		$this->items = User::items($id);

		$this->title = $this->user->username;		
		$this->loadLayout('users/show');
		
	}
	
	function update($page) {
		
		if (!isset($page)) {
			$page = 'password';
		}
		
		$this->user = User::get_by_id($_SESSION['user']['id']);

		$this->title = 'Settings';
		$this->loadLayout('users/update_'.$page);
		
	}
	
	function reset($code) {
		
		if (!empty($code)) {
			// Process reset
			
			// If two passwords submitted then check, otherwise show form
			if (isset($_POST['password1']) && isset($_POST['password2'])) {
				
				if (User::check_password_reset_code($code) == FALSE) {
					exit();
				}
				
				if ($_POST['password1'] == '' || $_POST['password2'] == '') {
					$error .= 'Please enter your password twice.<br />';
				}
				
				if ($_POST['password1'] != $_POST['password2']) {
					$error .= 'Passwords do not match.<br />';
				}
				
				// Error processing
				if ($error == '') {
					
					$user_id = User::check_password_reset_code($code);
					
					// Do update
					User::update_password($user_id, $_POST['password1']);
					
					$user = User::get_by_id($user_id);
					
					// Start session
					$_SESSION['user'] = $user;
					
					// Log login
					if (isset($this->plugins->log)) {
						$this->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'login');
					}
					
					// If redirect_to is set then redirect
					if ($_GET['redirect_to']) {
						header('Location: '.$_GET['redirect_to']);
						exit();
					}
					
					// Set welcome message
					$this->message = urlencode('Password updated.<br />Welcome back to '.$this->config->name.'!');
					
					// Go forth!
					if (SITE_IDENTIFIER == 'live') {
						header('Location: '.$this->config->url.'?message='.$this->message);
					} else {
						header('Location: '.$this->config->dev_url.'?message='.$this->message);
					}
					
					exit();
					
				} else {
					// Show error message
					
					$this->message = $error;
					$this->loadView('partials/header');
					if (User::check_password_reset_code($code) != FALSE) {
						$this->loadView('reset');
					}
					$this->loadView('partials/footer');
					
				}
				
			} else {
				// Code present so show password reset form
				
				$this->loadLayout('users/reset');
				
			}
			
		} else {
			// No code in URL so show new reset form
			
			$this->loadLayout('users/reset_new');
			
		}
		
	}
	
	function confirm($email) {
		
		
		
	}
	
	function json($username) {
		
		$user['user'] = User::get_by_username($username);
		$user['items'] = User::items($user['user']->id);
		$this->json = $user;
		$this->loadView('pages/json');
		
	}
	
}

?>