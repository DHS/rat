<?php

class help {
	
	function index() {
		
		global $app;
		
		$app->page->name = 'Help';
		$app->loadLayout('help');
		
	}
	
}

?>