<?php

require_once 'config/init.php';

if (!empty($_SESSION['user'])) {
	// do logout

	$user_id = $_SESSION['user']['id'];

	session_unset();
	session_destroy();

	// log logout
	if (isset($GLOBALS['log']))
		$GLOBALS['log']->add($user_id, 'user', NULL, 'logout');

	$_SESSION['user'] = array();
	
	$message = 'You are now logged out.';
	
}

// Header

//$page['name'] = 'Logout';
$app->loadView('header');

// Show login form

$app->loadView('login');

// Footer

$app->loadView('footer');

?>