<?php

// Header

$app->page->name = $app->config->tagline;

// Intro paragraph

//$app->loadView('index');

// New item form

$app->loadView('items_add');

// If friends is enabled, show feed of their activity

if ($app->config->friends['enabled'] == TRUE) {

	if (empty($_SESSION['user'])) {
		
		echo '<p>Please <a href="/login">login</a>.</p>';
		
	} else {
		
		// Show feed of friends' activity
		
		$app->page->items = $app->item->list_feed();
		$app->loadView('items_list');
		
	}

} else {
	
	// Friends not enabled so don't show recent items

	$app->page->items = $app->item->list_all();
	$app->loadView('items_list');	
	
}



?>
