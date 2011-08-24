<?php

class SettingsController extends Application {
	
	function index() {

		$this->loadPartial('header');
		
		// Show profile info form
		$this->loadView('settings_profile');
		
		// Show password change form
		$this->loadView('settings_password');
		
		$this->loadPartial('footer');
		
	}
	
}

/*

function index() {
	
}

function password() {
	// Change password
	
	if ($_POST['old_password'] != '' && $_POST['new_password1'] != '' && $_POST['new_password2'] != '') {
		// Check variables are present
		
		if (md5($_POST['old_password'].$this->config->encryption_salt) == $_SESSION['user']['password']) {
			// Check old passwords match
			
			if ($_POST['new_password1'] == $_POST['new_password2']) {
				// New passwords match
				
				// Call user_password_update in user model
				User::update_password($_SESSION['user']['id'], $_POST['new_password1']);
				
				// Update session
				$_SESSION['user']['password'] = md5($_POST['new_password1'].$this->config->encryption_salt);
				
				// Log password update
				if (isset($this->plugins->log))
					$this->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'change_password');

				$page['message'] = 'Password udpated!';
				
			} else {
				// New passwords don't match
				$page['message'] = 'There was a problem, please try again.';
			}
			
		} else {
			// Old passwords don't match
			$page['message'] = 'There was a problem, please try again.';
		}
		
	} else {
		// Variables missing
		$page['message'] = 'There was a problem, please try again.';
	}
	
	$this->loadView('partials/message');
	
}

function profile() {
	// Update profile
	
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
		if (User::check_contains_spaces($_POST['url']) == TRUE)
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
			User::update_profile($_SESSION['user']['id'], $_POST['name'], $_POST['bio'], $_POST['url']);
		
			// Set success message
			$page['message'] = 'Profile information updated!';
			
		} else {
			
			$page['message'] = $error;
			
		}

		// Show message
		$this->loadView('partials/message');
		
	}
	
}


// Selector

if (isset($_GET['page']))
	$page['selector'] = $_GET['page'];
if (!isset($page['selector']))
	$page['selector'] = 'index';

// Header

$itemSettings';
$this->loadView('partials/header');

// Show page determined by selector

call_user_func($page['selector']);

// Show profile info form

$this->loadView('settings_profile');

// Show password change form

$this->loadView('settings_password');

// Gravatar settings

if (isset($this->plugins->gravatar))
	$this->plugins->gravatar->show_settings($_SESSION['user']['email']);

// Footer

$this->loadView('partials/footer');

*/

?>
