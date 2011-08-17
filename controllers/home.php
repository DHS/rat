<?php

class home {
	
	function index($id = NULL) {
		
		global $app;
		
		$app->page->items = $app->item->list_all();
		$app->loadLayout('items_list');
		
	}
	
}

?>