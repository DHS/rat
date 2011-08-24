<?php

class InvitesController extends Application {
	
	function index() {
		
		$this->page['invites_remaining'] = $_SESSION['user']['invites'];
		$this->page['invites'] = Invite::list_sent($_SESSION['user']['id']);
		
		$this->page['name'] = 'Invites';
		$this->loadLayout('invites/index');
		
	}
	
	function add() {
		
		
		
	}
	
	function remove() {
		
		
		
	}
	
}

/*

//	Critical: Feature must be enabled and user must be logged in

if ($this->config->invites['enabled'] == FALSE || empty($_SESSION['user'])) {
	
	$this->page['name'] = 'Page not found';
	$this->loadView('partials/header');
	$this->loadView('partials/footer');
	exit;
	
}

// Header

$this->page['name'] = 'Invites';
$this->loadView('partials/header');

// Process new invites

if (isset($_POST['email'])) {
	
	if ($_SESSION['user']['invites'] < 1)
		$error .= 'You don\'t have any invites remaining.<br />';

	// Check if email contains spaces
	if (User::check_contains_spaces($_POST['email']) == TRUE)
		$error .= 'Email address cannot contain spaces.<br />';

	// Check if already invited
	if (Invite::check_invited($_SESSION['user']['id'], $_POST['email']) == TRUE)
		$error .= 'You have already invited this person.<br />';
	
	// Check if already a user
	if (User::get_by_email($_POST['email']) == TRUE)
		$error .= 'This person is already using '.$this->config->name.'!<br />';

	if ($error == '') {
		// no problems so do signup + login

		// add invite to database
		$id = Invite::add($_SESSION['user']['id'], $_POST['email']);

		// decrement invites in users table
		User::update_invites($_SESSION['user']['id'], -1);

		// award points
		if (isset($this->plugins->points))
			$this->plugins->points->update($_SESSION['user']['id'], $this->plugins->points['per_invite_sent']);

		// log invite
		if (isset($this->plugins->log))
			$this->plugins->log->add($_SESSION['user']['id'], 'invite', $id, 'add', $_POST['email']);

		if (SITE_IDENTIFIER == 'live') {
			$to		= "{$_POST['username']} <{$_POST['email']}>";
		} else {
			$to		= "{$_POST['username']} <davehs@gmail.com>";
		}

		$link = $this->config->url.'signup.php?code='.$id.'&email='.urlencode($_POST['email']);
		$headers = "From: {$_SESSION['user']['username']} <{$_SESSION['user']['email']}>\r\nBcc: davehs@gmail.com\r\nContent-type: text/html\r\n";

		// Load subject and body from template
		$this->loadView('email/invite_friend');

		if ($this->config->send_emails == TRUE) {
			// Email user
			mail($to, $subject, $body, $headers);
		}

		$this->message = 'Invite sent!';
		$this->loadView('partials/message');

	} else {
		
		$_GET['email'] = $_POST['email'];
		
		$this->message = $error;
		$this->loadView('partials/message');
		
	}
	
}

// Show invite form

$invites_remaining = $_SESSION['user']['invites'];
$this->loadView('invites/index');

// Show sent invites

$this->page['invites'] = Invite::list_sent($_SESSION['user']['id']);
$this->loadView('invites_list');

// Footer

$this->loadView('partials/footer');

*/

?>