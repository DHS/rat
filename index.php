<?php

require_once 'config/init.php';

// Header

$page['name'] = $app['tagline'];
include 'themes/'.$GLOBALS['app']['theme'].'/header.php';

// Intro paragraph

//include 'themes/'.$GLOBALS['app']['theme'].'/index.php';

// New item form

include 'themes/'.$GLOBALS['app']['theme'].'/items_add.php';

// If friends is enabled, show feed of their activity

if ($GLOBALS['app']['friends']['enabled'] == TRUE) {

	if (empty($_SESSION['user'])) {
		
		echo '<p>Please <a href="login.php">login</a>.</p>';
		
	} else {
		
		// Show feed of friends' activity
		
		$items = items_get_feed($_SESSION['user']['id']);
		include 'themes/'.$GLOBALS['app']['theme'].'/items_index.php';
		
	}

} else {
	
	// Friends not enabled so don't show recent items

	$items = items_get();
	include 'themes/'.$GLOBALS['app']['theme'].'/items_index.php';	
	
}

// Footer

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>