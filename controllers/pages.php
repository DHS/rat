<?php

class PagesController {
	
	function index() {
		
		global $app;
		
		// Not needed?
		
	}
	
	function show($name) {
		
		global $app;
		
		$app->page->name = ucfirst($name);
		
		$app->loadLayout('pages/'.$name);
		
	}
	
}

?>