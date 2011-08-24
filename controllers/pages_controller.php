<?php

class PagesController extends Application {
	
	function index() {
		
		// Not needed?
		
	}
	
	function show($name) {
		
		$this->title = ucfirst($name);
		
		$this->loadLayout('pages/'.$name);
		
	}
	
}

?>