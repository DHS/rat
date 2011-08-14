<?php

class help {
	
	function index() {
		
		global $app;
		
		// Header
		$app->page->name = 'Help';
		$app->loadView('header');

		// Content
		$app->loadView('help');

		// Footer
		$app->loadView('footer');
		
	}
	
}

?>