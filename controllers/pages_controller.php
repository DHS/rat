<?php

class PagesController extends Application {
	
	function index() {
		
		// Not needed?
		
	}
	
	function show($name) {
		
		$this->page['name'] = ucfirst($name);
		
		$this->loadLayout('pages/'.$name);
		
	}
	
}

?>