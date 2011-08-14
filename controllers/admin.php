<?php

// Critical: Setup wizard creates admin as first user

if (count($app->admin->list_users()) == 0 && $_GET['page'] == '') {
	
	$app->page->name = 'Setup';
	
	$_GET['id'] = 1;
	
	$app->page->message = 'Welcome to Rat! Please enter your details:';
	$app->loadView('header');
	$app->loadView('admin_setup');
	$app->loadView('footer');
	
	exit();
	
} elseif (count($app->admin->list_users()) == 0 && $_GET['page'] == 'invite') {
	
	$app->page->name = 'Setup';
	
	// Do signup

	$user_id = $app->user->add($_POST['email']);
	$app->user->signup($user_id, $_POST['username'], $_POST['password']);
	
	$user = $app->user->get_by_email($_POST['email']);
	$_SESSION['user'] = $user;
	
	// Log login
	if (isset($app->plugins->log))
		$app->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'signup');
	
	$app->page->message = 'Rat is now setup and you are logged in!';
	
	// Go forth!
	if (SITE_IDENTIFIER == 'live') {
		header('Location: '.$app->config->url.'?message='.urlencode($app->page->message));
	} else {
		header('Location: '.$app->config->dev_url.'?message='.urlencode($app->page->message));
	}
		
	exit();
	
}

//	Critical: User must have admin capability

if (in_array($_SESSION['user']['id'], $app->config->admin_users) != TRUE) {

	$app->page->name = 'Page not found';
	$app->loadView('header');
	$app->loadView('footer');
	exit;

}

/*	
*	Admin page functions
*
*		1. Dashboard
*		2. List beta signups
*		3. Invite beta signups
*		4. List users
*		5. Give users invites
*	
*/

function dashboard() {
	
	global $app;
	
	$app->page->users = $app->admin->list_users();
	$app->page->users_beta = $app->admin->list_users_beta();
	
	$app->loadView('admin_dashboard');
	
}

function signups() {
	
	global $app;
	
	$app->page->users = $app->admin->list_users_beta();
	
	$app->loadView('admin_signups');
	
}

function invite() {

	global $app;

	if ($_GET['email'] != '') {
		
		// add invite to database
		$id = $app->invite->add($_SESSION['user']['id'], $_GET['email']);
		
		// log invite
		if (isset($app->plugins->log))
			$app->plugins->log->add($_SESSION['user']['id'], 'invite', $id, 'admin_add', $_GET['email']);
		
		if (SITE_IDENTIFIER == 'live') {
			$to		= "{$_POST['username']} <{$_GET['email']}>";
		} else {
			$to		= "{$_POST['username']} <davehs@gmail.com>";
		}
		
		$link		= $app->config->url.'signup.php?code='.$id.'&email='.urlencode($_GET['email']);
		$headers	= "From: {$_SESSION['user']['username']} <{$_SESSION['user']['email']}>\r\nContent-type: text/html\r\n";
		
		// Load template into $body variable
		$app->loadView('email_invite_admin');
		
		if ($app->config->send_emails == TRUE) {
			// Email user
			mail($to, $subject, $body, $headers);
		}
		
		$app->page->message = 'User invited!';
		$app->loadView('message');
		
		signups();
		
	}
	
}

function users() {
	
	global $app;
	
	$app->page->users = $app->admin->list_users();
	
	$app->loadView('admin_users');

}

function grant_invites() {
	
	global $app;
		
	if ($_GET['count'] > 0) {
		
		$app->admin->update_invites($_GET['count']);
		
		$app->page->message = 'Invites updated!';
		$app->loadView('message');
		
		users();
		
	}
	
}

function history() {
	// Should be log() but that's a native PHP function
	// Show most recent entries in the log
	
	global $app;
	
	if (isset($app->plugins->log))
		$app->plugins->log->view();

}

// Selector

$app->page->selector = $_GET['page'];
if ($app->page->selector == NULL)
	$app->page->selector = 'dashboard';

// Header

$app->page->name = 'Admin - '.ucfirst(strtolower($app->page->selector));
$app->loadView('header');
$app->loadView('admin_menu');

// Show page determined by selector

call_user_func($app->page->selector);

// Footer

$app->loadView('footer');

?>