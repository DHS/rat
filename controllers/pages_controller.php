<?php

class PagesController {
	
	function index() {
		
		global $app;
		
		// Not needed?
		
	}
	
	function show($name) {
		
		global $app;
		
		$app->page->name = ucfirst($name);
		
		$this->loadLayout('pages/'.$name);
		
	}
	
}

?>