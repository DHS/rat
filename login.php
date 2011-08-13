<?php

require_once 'config/init.php';

//	Critical: If email + pass ok then login

if ($_POST['email'] && $_POST['password']) {
		
	$user = $app->user->get_by_email($_POST['email']);
	$encrypted_password = md5($_POST['password'].$app->config->encryption_salt);
	
	if ($user['password'] == $encrypted_password) {
		
		$_SESSION['user'] = $user;

		// Log login
		if (isset($GLOBALS['log']))
			$GLOBALS['log']->add($_SESSION['user']['id'], 'user', NULL, 'login');

		// Get redirected
		if ($_GET['redirect_to']) {
			header('Location: '.$_GET['redirect_to']);
			exit();
		}

		// Go forth
		if (SITE_IDENTIFIER == 'live') {
			header('Location: '.$app->config->url);
		} else {
			header('Location: '.$app->config->dev_url);
		}
		
		exit();
		
	} else {
		
		$message .= 'Something isn\'t quite right.<br />Please try again...';
		$email = $_POST['email'];
		
	}
	
}

// Header

//$page['name'] = 'Login';
include 'themes/'.$app->config->theme.'/header.php';

// Show login form

if (empty($_SESSION['user'])) {
	include 'themes/'.$app->config->theme.'/login.php';
} else {
	$message = 'You are already logged in!<br /><a href="logout.php">Click here</a> to logout.';
	include 'themes/'.$app->config->theme.'/message.php';
}

// Footer

include 'themes/'.$app->config->theme.'/footer.php';



?>