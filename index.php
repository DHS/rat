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
		
		$items = $item->list_feed();
		include 'themes/'.$GLOBALS['app']['theme'].'/items_list.php';
		
	}

} else {
	
	// Friends not enabled so don't show recent items

	$items = $item->list_all();
	include 'themes/'.$GLOBALS['app']['theme'].'/items_list.php';	
	
}

// Footer

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>