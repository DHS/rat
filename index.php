<?php

require_once 'config/init.php';

// Header

$page['name'] = $app->tagline;
$app->loadView('header');

// Intro paragraph

//$app->loadView('index');

// New item form

$app->loadView('items_add');

// If friends is enabled, show feed of their activity

if ($app->friends['enabled'] == TRUE) {

	if (empty($_SESSION['user'])) {
		
		echo '<p>Please <a href="login.php">login</a>.</p>';
		
	} else {
		
		// Show feed of friends' activity
		
		$items = $app->item->list_feed();
		$app->loadView('items_list');
		
	}

} else {
	
	// Friends not enabled so don't show recent items

	$items = $app->item->list_all();
	$app->loadView('items_list');	
	
}

// Footer

$app->loadView('footer');

?>