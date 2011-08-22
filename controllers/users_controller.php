<?php

class UsersController {
	
	// Show a list of users
	function index() {
		
		global $app;
		
		// Not needed?
		
	}
	
	// Add a user
	function add() {
		
		global $app;
		
		$app->loadLayout('users/add_beta');
		
	}
	
	// Show a user
	function show($id) {
		
		global $app;
		
		$app->page->user = $app->user->get($id);
		$app->page->items = $app->item->list_user($id);
		
		$app->loadLayout('users/show');
		
	}
	
	function update($id) {
		
		global $app;
		
		$app->page->user = $app->user->get($id);
		
		$app->loadLayout('users/update');
		
	}
	
	function reset($code) {
		
		global $app;
		
		if (!empty($code)) {
			// Process reset
			
			// If two passwords submitted then check, otherwise show form
			if (isset($_POST['password1']) && isset($_POST['password2'])) {
				
				if ($app->user->check_password_reset_code($code) == FALSE)
					exit();
				
				if ($_POST['password1'] == '' || $_POST['password2'] == '')
					$error .= 'Please enter your password twice.<br />';
				
				if ($_POST['password1'] != $_POST['password2'])
					$error .= 'Passwords do not match.<br />';
				
				// Error processing
				if ($error == '') {
					
					$user_id = $app->user->check_password_reset_code($code);
					
					// Do update
					$app->user->update_password($user_id, $_POST['password1']);
					
					$user = $app->user->get($user_id);
					
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
					$app->page->message = urlencode('Password updated.<br />Welcome back to '.$app->config->name.'!');
					
					// Go forth!
					if (SITE_IDENTIFIER == 'live') {
						header('Location: '.$app->config->url.'?message='.$app->page->message);
					} else {
						header('Location: '.$app->config->dev_url.'?message='.$app->page->message);
					}
					
					exit();
					
				} else {
					// Show error message
					
					$app->page->message = $error;
					$app->loadView('header');
					if ($app->user->check_password_reset_code($code) != FALSE)
						$app->loadView('reset');
					$app->loadView('footer');
					
				}
				
			} else {
				// Code present so show password reset form
				
				$app->loadLayout('users/reset');
				
			}
			
		} else {
			// No code in URL so show new reset form
			
			$app->loadLayout('users/reset_new');
			
		}
		
	}
	
	function confirm($email) {
		
		global $app;
		
		
		
	}
	
	function json($username) {
		
		global $app;
		
		$user['user'] = $app->user->get_by_username($username);
		$user['items'] = $app->item->list_user($user['user']['id']);
		$app->page->json = $user;
		$app->loadView('pages/json');
		
	}
	
}

?>