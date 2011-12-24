<?php

class PagesController extends Application {
	
	function show($name) {
		
		$this->title = ucfirst($name);
		
		$this->loadView('pages/'.$name);
		
	}
	
}

?>