<?php

class ItemsController {
	
	// Show stream of everyone's items
	function index() {
		
		global $app;
		
		$app->page->name = $app->config->tagline;
		$app->page->items = $app->item->list_all();
		
		$app->loadLayout('items');
		
	}
	
	// Add an item
	function add() {
		
		global $app;
		
		$app->loadLayout('items/add');
		
	}
	
	// Show a single item
	function show($id) {
		
		global $app;
		
		$app->page->item = $app->item->get($id);
		
		$app->loadLayout('items/single');
		
	}
	
	// Show feed of friends' new items
	function feed() {
		
		global $app;
		
		if ($app->config->friends['enabled'] == TRUE) {
			
			// If friends enabled then show feed of friends' activity
			
			$app->page->name = $app->config->tagline;
			$app->page->items = $app->item->list_feed($_SESSION['user']['id']);
			$app->loadLayout('items');
			
		} else {
			
			// Friends not enabled so fall back to showing everyone's activity
			
			$this->index();
			
		}
		
	}
	
}

?>