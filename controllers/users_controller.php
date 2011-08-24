<?php

class UsersController extends Application {
	
	function __construct() {
		
		global $app;
		
		// Check if user is logged in and trying to signup
		if ($app->uri['action'] == 'add' && !empty($_SESSION['user'])) {

			$page['name'] = 'Signup';
			$page['message'] = 'You are already logged in!';
			$this->loadView('partials/header');
			$this->loadView('partials/footer');
			exit;

		}
		
	}
	
	// Show a list of users
	function index() {
		
		global $app;
		
		// Not needed?
		
	}
	
	// Add a user
	function add($code) {
		
		global $app;
		
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
					$page['code'] = $code;
				}
				
				$this->loadLayout('users/add');
			}
			
		}
		
	}
	
	// Show a user
	function show($id) {
		
		global $app;
		
		$page['user'] = User::get($id);
		$page['items'] = Item::list_user($id);
		
		$page['name'] = $page['user']['username'];
		$this->loadLayout('users/show');
		
	}
	
	function update($id) {
		
		global $app;
		
		$page['user'] = User::get($id);
		
		$page['name'] = 'Settings';
		$this->loadLayout('users/update');
		
	}
	
	function reset($code) {
		
		global $app;
		
		if (!empty($code)) {
			// Process reset
			
			// If two passwords submitted then check, otherwise show form
			if (isset($_POST['password1']) && isset($_POST['password2'])) {
				
				if (User::check_password_reset_code($code) == FALSE)
					exit();
				
				if ($_POST['password1'] == '' || $_POST['password2'] == '')
					$error .= 'Please enter your password twice.<br />';
				
				if ($_POST['password1'] != $_POST['password2'])
					$error .= 'Passwords do not match.<br />';
				
				// Error processing
				if ($error == '') {
					
					$user_id = User::check_password_reset_code($code);
					
					// Do update
					User::update_password($user_id, $_POST['password1']);
					
					$user = User::get($user_id);
					
					// Start session
					$_SESSION['user'] = $user;
					
					// Log login
					if (isset($app->plugins->log))
						$app->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'login');
					
					// If redirect_to is set then redirect
					if ($_GET['redirect_to']) {
						header('Location: '.$_GET['redirect_to']);
						exit();
					}
					
					// Set welcome message
					$page['message'] = urlencode('Password updated.<br />Welcome back to '.$this->config->name.'!');
					
					// Go forth!
					if (SITE_IDENTIFIER == 'live') {
						header('Location: '.$this->config->url.'?message='.$page['message']);
					} else {
						header('Location: '.$this->config->dev_url.'?message='.$page['message']);
					}
					
					exit();
					
				} else {
					// Show error message
					
					$page['message'] = $error;
					$this->loadView('partials/header');
					if (User::check_password_reset_code($code) != FALSE)
						$this->loadView('reset');
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
		
		global $app;
		
		
		
	}
	
	function json($username) {
		
		global $app;
		
		$user['user'] = User::get_by_username($username);
		$user['items'] = Item::list_user($user['user']['id']);
		$page['json'] = $user;
		$this->loadView('pages/json');
		
	}
	
}

?>