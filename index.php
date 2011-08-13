<?php

require_once 'config/init.php';

// Header

$app->page->name = $app->config->tagline;
include 'themes/'.$app->config->theme.'/header.php';

// Intro paragraph

//include 'themes/'.$app->config->theme.'/index.php';

// New item form

include 'themes/'.$app->config->theme.'/items_add.php';

// If friends is enabled, show feed of their activity

if ($app->config->friends['enabled'] == TRUE) {

	if (empty($_SESSION['user'])) {
		
		echo '<p>Please <a href="login.php">login</a>.</p>';
		
	} else {
		
		// Show feed of friends' activity
		
		$app->page->items = $app->item->list_feed();
		include 'themes/'.$app->config->theme.'/items_list.php';
		
	}

} else {
	
	// Friends not enabled so don't show recent items

	$app->page->items = $app->item->list_all();
	include 'themes/'.$app->config->theme.'/items_list.php';	
	
}

// Footer

include 'themes/'.$app->config->theme.'/footer.php';

?>