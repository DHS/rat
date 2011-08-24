<?php

require_once 'config/init.php';

function show_form() {
	
	global $app;
	
	$app->loadView('partials/header');
	$app->loadView('reset_begin');
	$app->loadView('partials/footer');
	
}

function generate_code() {
	
	global $app;
	
	$user = User::get_by_email($_POST['email']);
	
	// Check is a user
	if ($user != NULL) {
		
		// Generate code
		$code = User::generate_password_reset_code($user['id']);
		
		$to = $_POST['email'];
		$link = $app->config->url.'forgot.php?code='.$code;
		$headers = "From: $app->config->name <robot@blah.com>\r\nContent-type: text/html\r\n";
		
		// Load subject and body from template
		$app->loadView('email/password_reset');
		
		// Email user
		if ($app->config->send_emails == TRUE)
			mail($to, $subject, $body, $headers);
		
	}
	
	$app->loadView('partials/header');
	$app->page->message = 'Check your email for instructions about how to reset your password!';
	$app->loadView('partials/message');
	$app->loadView('partials/footer');
		
}

function check_code() {
	
	global $app;
	
	$app->loadView('partials/header');
	
	if (User::check_password_reset_code($_GET['code']) != FALSE)
		$app->loadView('reset_confirm');

	$app->loadView('partials/footer');
	
}

function update_password() {

	global $app;
	
	// Sneaky
	if (User::check_password_reset_code($_POST['code']) == FALSE)
		exit();
	
	if ($_POST['password1'] == '' || $_POST['password2'] == '')
		$error .= 'Please enter your password twice.<br />';
		
	if ($_POST['password1'] != $_POST['password2'])
		$error .= 'Passwords do not match.<br />';
		
	// Error processing
	if ($error == '') {
		
		$user_id = User::check_password_reset_code($_POST['code']);
		
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
		$app->page->message = urlencode('Password updated.<br />Welcome back to '.$app->config->name.'!');
		
		// Go forth!
		if (SITE_IDENTIFIER == 'live') {
			header('Location: '.$app->config->url.'?message='.$app->page->message);
		} else {
			header('Location: '.$app->config->dev_url.'?message='.$app->page->message);
		}

		exit();
		
	} else {
		
		$app->page->message = $error;
		$app->loadView('partials/header');
		if (User::check_password_reset_code($_POST['code']) != FALSE)
			$app->loadView('reset_confirm');
		$app->loadView('partials/footer');
		
	}
	
}

// Selector
if (isset($_POST['email'])) {
	
	generate_code();
	
} elseif (isset($_GET['code'])) {
	
	check_code();
	
} elseif (isset($_POST['code']) && isset($_POST['password1']) && isset($_POST['password2'])) {
	
	update_password();
	
} else {

	show_form();
	
}

?>