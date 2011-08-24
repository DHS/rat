<?php

require_once 'config/init.php';

function show_form() {
	
	$this->loadView('partials/header');
	$this->loadView('reset_begin');
	$this->loadView('partials/footer');
	
}

function generate_code() {
	
	$user = User::get_by_email($_POST['email']);
	
	// Check is a user
	if ($user != NULL) {
		
		// Generate code
		$code = User::generate_password_reset_code($user['id']);
		
		$to = $_POST['email'];
		$link = $this->config->url.'forgot.php?code='.$code;
		$headers = "From: $this->config->name <robot@blah.com>\r\nContent-type: text/html\r\n";
		
		// Load subject and body from template
		$this->loadView('email/password_reset');
		
		// Email user
		if ($this->config->send_emails == TRUE)
			mail($to, $subject, $body, $headers);
		
	}
	
	$this->loadView('partials/header');
	$this->page['message'] = 'Check your email for instructions about how to reset your password!';
	$this->loadView('partials/message');
	$this->loadView('partials/footer');
		
}

function check_code() {
	
	$this->loadView('partials/header');
	
	if (User::check_password_reset_code($_GET['code']) != FALSE)
		$this->loadView('reset_confirm');

	$this->loadView('partials/footer');
	
}

function update_password() {

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
		if (isset($this->plugins->log))
			$this->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'login');
		
		// If redirect_to is set then redirect
		if ($_GET['redirect_to']) {
			header('Location: '.$_GET['redirect_to']);
			exit();
		}
		
		// Set welcome message
		$this->page['message'] = urlencode('Password updated.<br />Welcome back to '.$this->config->name.'!');
		
		// Go forth!
		if (SITE_IDENTIFIER == 'live') {
			header('Location: '.$this->config->url.'?message='.$this->page['message']);
		} else {
			header('Location: '.$this->config->dev_url.'?message='.$this->page['message']);
		}

		exit();
		
	} else {
		
		$this->page['message'] = $error;
		$this->loadView('partials/header');
		if (User::check_password_reset_code($_POST['code']) != FALSE)
			$this->loadView('reset_confirm');
		$this->loadView('partials/footer');
		
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