<?php

class PagesController extends Application {
	
	function index() {
		
		// Not needed?
		
	}
	
	function show($name) {
		
		$page['name'] = ucfirst($name);
		
		$this->loadLayout('pages/'.$name);
		
	}
	
}

?>