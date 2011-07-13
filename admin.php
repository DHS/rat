<?php

require_once 'config/init.php';

// Critical: Setup wizard creates admin as first user

if (count(admin_get_users()) == 0 && $_GET['page'] == '') {
	
	$page['name'] = 'Setup';
	
	$_GET['id'] = 1;
	$password = generate_password();
	
	$message = 'Welcome to Rat! Please enter your details:';
	include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
	include 'themes/'.$GLOBALS['app']['theme'].'/admin_user_add.php';
	include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';
	
	exit();
	
} elseif (count(admin_get_users()) == 0 && $_GET['page'] == 'invite') {
	
	$page['name'] = 'Setup';
	
	// Do signup

	$user_id = user_add($_POST['email']);
	user_signup($user_id, $_POST['username'], $_POST['password']);
	
	$user = user_get_by_email($_POST['email']);
	$_SESSION['user'] = $user;
	
	// Log login
	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_SESSION['user']['id'], 'user', NULL, 'signup');
	
	$message = 'Rat is now setup and you are logged in!';
	
	// Go forth!
	if (SITE_IDENTIFIER == 'live') {
		header('Location: '.$GLOBALS['app']['url'].'?message='.urlencode($message));
	} else {
		header('Location: '.$GLOBALS['app']['dev_url'].'?message='.urlencode($message));
	}
		
	exit();
	
}

//	Critical: User must have admin capability

if (in_array($_SESSION['user']['id'], $app['admin_users']) != TRUE) {

	$page['name'] = 'Page not found';
	include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
	include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';
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
	
	$user_count = count(admin_get_users());
	$waiting_user_count = count(admin_get_waiting_users());
	
	include 'themes/'.$GLOBALS['app']['theme'].'/admin_dashboard.php';
	
}

function signups() {
	
	$waiting_users = admin_get_waiting_users();
	$waiting_user_count = count($waiting_users);
	
	include 'themes/'.$GLOBALS['app']['theme'].'/admin_signups.php';
	
}

function invite() {

	if ($_GET['email'] != '') {
		
		// add invite to database
		$id = invites_add($_SESSION['user']['id'], $_GET['email']);
		
		// log invite
		if (is_object($GLOBALS['log']))
			$GLOBALS['log']->add($_SESSION['user']['id'], 'invite', $id, 'admin_add', $_GET['email']);
		
		if (SITE_IDENTIFIER == 'live') {
			$to		= "{$_POST['username']} <{$_GET['email']}>";
		} else {
			$to		= "{$_POST['username']} <davehs@gmail.com>";
		}
		
		$url		= $GLOBALS['app']['url'].'signup.php?code='.$id.'&email='.urlencode($_GET['email']);
		
		$subject	= "Your {$GLOBALS['app']['name']} invite is here!";
		// Load template into $body variable
		include 'themes/'.$GLOBALS['app']['theme'].'/email_invite_admin.php';
		$headers	= "From: {$_SESSION['user']['username']} <{$_SESSION['user']['email']}>\r\nContent-type: text/html\r\n";
		
		if ($GLOBALS['app']['send_emails'] == TRUE) {
			// Email user
			mail($to, $subject, $body, $headers);
		}
		
		$message = 'User invited!';
		include 'themes/'.$GLOBALS['app']['theme'].'/message.php';
		
		signups();
		
	}
	
}

function users() {
	
	$users = admin_get_users();
	$user_count = count($users);
	
	include 'themes/'.$GLOBALS['app']['theme'].'/admin_users.php';

}

function grant_invites() {
			
	if ($_GET['count'] > 0) {
		
		admin_grant_invites($_GET['count']);
		
		$message = 'Invites updated!';
		include 'themes/'.$GLOBALS['app']['theme'].'/message.php';
		
		users();
		
	}
	
}

function generate_password() {
	$password = '';
	$source = 'abcdefghijklmnpqrstuvwxyz123456789';
	while (strlen($password) < 6) {
		$password .= $source[rand(0, strlen($source))];
	}
	return $password;
}


// Selector

$page['selector'] = $_GET['page'];
if ($page['selector'] == NULL)
	$page['selector'] = 'dashboard';

// Header

$page['name'] = 'Admin - '.ucfirst(strtolower($page['selector']));
include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
include 'themes/'.$GLOBALS['app']['theme'].'/admin_menu.php';

// Show page determined by selector

$page['selector']();

// Footer

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>