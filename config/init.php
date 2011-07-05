<?php

// Config file contains lots of handy variables
require_once 'config/config.php';

// Setup database
require_once 'config/database.php';

// Start session
session_start();

// Load admin model
require_once 'models/admin.php';

if ($app['private'] == TRUE && count(admin_get_users()) != 0) {
	// App requires login to view pages

	// Finds page name
	preg_match("/[a-zA-Z0-9]+\.php/", $_SERVER['PHP_SELF'], $result);
	
	if (empty($_SESSION['user']) && in_array($result[0], $app['public_pages']) != TRUE) {
		// User not logged in and page is not in $app['public_pages'] so show holding page

		include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
		include 'themes/'.$GLOBALS['app']['theme'].'/splash.php';
		include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';
	
		// Stop processing the rest of the page
		exit();
	
	}

}

// Load other models
require_once 'models/user.php';
require_once 'models/invites.php';
require_once 'models/friends.php';
require_once 'models/items.php';
require_once 'models/comments.php';
require_once 'models/likes.php';

?>