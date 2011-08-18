<?php

class HomeController {
	
	// Show stream of everyone's activity
	function index() {
		
		global $app;
		
		$app->page->name = $app->config->tagline;
		$app->page->items = $app->item->list_all();
		
		$app->loadLayout('items_list');
		
	}
	
	// Show feed of friends' activity
	function feed() {
		
		global $app;
		
		if ($app->config->friends['enabled'] == TRUE) {
			
			// If friends enabled then show feed of friends' activity
			
			$app->page->name = $app->config->tagline;
			$app->page->items = $app->item->list_feed($_SESSION['user']['id']);
			$app->loadLayout('items_list');
			
		} else {
			
			// Friends not enabled so fall back to showing everyone's activity
			
			$this->index();
			
		}
		
	}
	
}

?>
