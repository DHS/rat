<?php

class SearchController {
	
	function index($q = NULL) {
		
		global $app;
		
		$app->loadLayout('search/add');
		if (isset($q))
			$this->show($q);
		
	}
	
	function show($q) {
		
		global $app;

		$app->page->items = $app->searches->do_search($q);
		$app->loadLayout('items/index');
		
	}
	
	function json($q) {
		
		global $app;
		
		$items['items'] = $app->searches->do_search($q);
		$app->page->json = $items;
		$app->loadView('pages/json');
		
	}

}

?>
