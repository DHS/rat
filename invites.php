<?php

require_once 'config/init.php';

//	Critical: Feature must be enabled and user must be logged in

if ($app['invites']['enabled'] == FALSE || empty($_SESSION['user'])) {
	
	$page['name'] = 'Page not found';
	include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
	include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';
	exit;
	
}

// Header

$page['name'] = 'Invites';
include 'themes/'.$GLOBALS['app']['theme'].'/header.php';

// Process new invites

if ($_POST['email'] != '') {
	
	if ($_SESSION['user']['invites'] < 1)
		$error .= 'You don\'t have any invites remaining.<br />';

	// Check if email contains spaces
	if ($user->check_contains_spaces($_POST['email']) == TRUE)
		$error .= 'Email address cannot contain spaces.<br />';

	// Check if already invited
	if ($invite->check_invited(($_SESSION['user']['id'], $_POST['email']) == TRUE)
		$error .= 'You have already invited this person.<br />';
	
	// Check if already a user
	if ($user->get_by_email($_POST['email']) == TRUE)
		$error .= 'This person is already using '.$GLOBALS['app']['name'].'!<br />';

	if ($error == '') {
		// no problems so do signup + login

		// add invite to database
		$id = $invite->add($_SESSION['user']['id'], $_POST['email']);

		// decrement invites in users table
		$user->update_invites($_SESSION['user']['id'], -1);

		// award points
		if (is_object($GLOBALS['points']))
			$GLOBALS['points']->update($_SESSION['user']['id'], $app['points']['per_invite_sent']);

		// log invite
		if (is_object($GLOBALS['log']))
			$GLOBALS['log']->add($_SESSION['user']['id'], 'invite', $id, 'add', $_POST['email']);

		if (SITE_IDENTIFIER == 'live') {
			$to		= "{$_POST['username']} <{$_POST['email']}>";
		} else {
			$to		= "{$_POST['username']} <davehs@gmail.com>";
		}

		$link = $GLOBALS['app']['url'].'signup.php?code='.$id.'&email='.urlencode($_POST['email']);

		$subject	= "[{$GLOBALS['app']['name']}] An invitation from {$_SESSION['user']['username']}";
		// Load template into $body variable
		include 'themes/'.$GLOBALS['app']['theme'].'/email_invite_friend.php';
		$headers	= "From: {$_SESSION['user']['username']} <{$_SESSION['user']['email']}>\r\nBcc: davehs@gmail.com\r\nContent-type: text/html\r\n";

		if ($GLOBALS['app']['send_emails'] == TRUE) {
			// Email user
			mail($to, $subject, $body, $headers);
		}

		$message = 'Invite sent!';
		include 'themes/'.$GLOBALS['app']['theme'].'/message.php';

	} else {
		
		$_GET['email'] = $_POST['email'];
		
		$message = $error;
		include 'themes/'.$GLOBALS['app']['theme'].'/message.php';
		
	}
	
}

// Show invite form

$invites_remaining = $_SESSION['user']['invites'];
include 'themes/'.$GLOBALS['app']['theme'].'/invites.php';

// Show sent invites

$invites_sent = $invite->list_sent($_SESSION['user']['id']);
include 'themes/'.$GLOBALS['app']['theme'].'/invites_list.php';

// Footer

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>