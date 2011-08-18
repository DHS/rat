<?php

class SettingsController {
	
	function index() {

		global $app;
		
		$app->page->name = 'Settings';
		$app->loadPartial('header');
		
		// Show profile info form
		$app->loadView('settings_profile');
		
		// Show password change form
		$app->loadView('settings_password');
		
		$app->loadPartial('footer');
		
	}
	
}

/*

function index() {
	
}

function password() {
	// Change password
	
	global $app;

	if ($_POST['old_password'] != '' && $_POST['new_password1'] != '' && $_POST['new_password2'] != '') {
		// Check variables are present
		
		if (md5($_POST['old_password'].$app->config->encryption_salt) == $_SESSION['user']['password']) {
			// Check old passwords match
			
			if ($_POST['new_password1'] == $_POST['new_password2']) {
				// New passwords match
				
				// Call user_password_update in user model
				$app->user->update_password($_SESSION['user']['id'], $_POST['new_password1']);
				
				// Update session
				$_SESSION['user']['password'] = md5($_POST['new_password1'].$app->config->encryption_salt);
				
				// Log password update
				if (isset($app->plugins->log))
					$app->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'change_password');

				$app->page->message = 'Password udpated!';
				
			} else {
				// New passwords don't match
				$app->page->message = 'There was a problem, please try again.';
			}
			
		} else {
			// Old passwords don't match
			$app->page->message = 'There was a problem, please try again.';
		}
		
	} else {
		// Variables missing
		$app->page->message = 'There was a problem, please try again.';
	}
	
	$app->loadView('message');
	
}

function profile() {
	// Update profile
	
	global $app;
	
	if ($_POST['name'] != '' || $_POST['bio'] != '' || $_POST['url'] != '') {
		// Even if only one field set, do update
		
		$error = '';
		
		// Validate URL
		
		// Check for empty URL. Default value: http://
		if ($_POST['url'] == 'http://') {
			$_POST['url'] = '';
		}
		
		// Ensure URL begins with http://
		if ($_POST['url'] != '' && (substr($_POST['url'], 0, 7) != 'http://' && substr($_POST['url'], 0, 8) != 'https://')) {
			$_POST['url'] = 'http://'.$_POST['url'];
		}
		
		// Check for spaces
		if ($app->user->check_contains_spaces($_POST['url']) == TRUE)
			$error = 'URL cannot contain spaces.';
		
		// End URL validation
		
		if ($error == '') {
		
			// Update session vars
			if ($_POST['name'] != '')
				$_SESSION['user']['name'] = $_POST['name'];
        	
			if ($_POST['bio'] != '')
				$_SESSION['user']['bio'] = $_POST['bio'];
				
			if ($_POST['url'] != '')
				$_SESSION['user']['url'] = $_POST['url'];
	    	
			// Call user_update_profile in user model
			$app->user->update_profile($_SESSION['user']['id'], $_POST['name'], $_POST['bio'], $_POST['url']);
		
			// Set success message
			$app->page->message = 'Profile information updated!';
			
		} else {
			
			$app->page->message = $error;
			
		}

		// Show message
		$app->loadView('message');
		
	}
	
}


// Selector

if (isset($_GET['page']))
	$app->page->selector = $_GET['page'];
if (!isset($app->page->selector))
	$app->page->selector = 'index';

// Header

$app->page->name = 'Settings';
$app->loadView('header');

// Show page determined by selector

call_user_func($app->page->selector);

// Show profile info form

$app->loadView('settings_profile');

// Show password change form

$app->loadView('settings_password');

// Gravatar settings

if (isset($app->plugins->gravatar))
	$app->plugins->gravatar->show_settings($_SESSION['user']['email']);

// Footer

$app->loadView('footer');

*/

?>
