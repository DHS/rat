<?php

class HelpController {
	
	function index() {
		
		global $app;
		
		$app->page->name = 'Help';
		$app->loadLayout('help');
		
	}
	
}

?>
