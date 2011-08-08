<?php

require_once 'config/init.php';

// Header

$page['name'] = $app->tagline;
include 'themes/'.$app->theme.'/header.php';

// Intro paragraph

//include 'themes/'.$app->theme.'/index.php';

// New item form

include 'themes/'.$app->theme.'/items_add.php';

// If friends is enabled, show feed of their activity

if ($app->friends['enabled'] == TRUE) {

	if (empty($_SESSION['user'])) {
		
		echo '<p>Please <a href="login.php">login</a>.</p>';
		
	} else {
		
		// Show feed of friends' activity
		
		$items = $app->item->list_feed();
		include 'themes/'.$app->theme.'/items_list.php';
		
	}

} else {
	
	// Friends not enabled so don't show recent items

	$items = $app->item->list_all();
	include 'themes/'.$app->theme.'/items_list.php';	
	
}

// Footer

include 'themes/'.$app->theme.'/footer.php';

?>