<?php

require_once 'config/init.php';

// Critical: user must be logged in

if ($_SESSION['user'] == NULL) {
	
	$page['name'] = 'Page not found';
	$message = 'Please <a href="login.php?redirect_to=/settings.php">login</a> to view your settings.';
	include 'themes/'.$app->config->theme.'/header.php';
	include 'themes/'.$app->config->theme.'/footer.php';
	exit();
	
}

/*
*	Settings functions:
*
*		Show settings page
*		Change password
*		Update profile info
*		
*/

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
				if (isset($GLOBALS['log']))
					$GLOBALS['log']->add($_SESSION['user']['id'], 'user', NULL, 'change_password');

				$message = 'Password udpated!';
				
			} else {
				// New passwords don't match
				$message = 'There was a problem, please try again.';
			}
			
		} else {
			// Old passwords don't match
			$message = 'There was a problem, please try again.';
		}
		
	} else {
		// Variables missing
		$message = 'There was a problem, please try again.';
	}
	
	include 'themes/'.$app->config->theme.'/message.php';
	
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
			$message = 'Profile information updated!';
			
		} else {
			
			$message = $error;
			
		}

		// Show message
		include 'themes/'.$app->config->theme.'/message.php';
		
	}
	
}


// Selector

$page['selector'] = $_GET['page'];
if ($page['selector'] == NULL)
	$page['selector'] = 'index';

// Header

$page['name'] = 'Settings';
include 'themes/'.$app->config->theme.'/header.php';

// Show page determined by selector

$page['selector']();

// Show profile info form

include 'themes/'.$app->config->theme.'/settings_profile.php';

// Show password change form

include 'themes/'.$app->config->theme.'/settings_password.php';

// Gravatar settings

if (isset($GLOBALS['gravatar']))
	$GLOBALS['gravatar']->show_settings($_SESSION['user']['email']);

// Footer

include 'themes/'.$app->config->theme.'/footer.php';

?>