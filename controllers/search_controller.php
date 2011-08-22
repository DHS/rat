<?php

class SearchController {
	
	function index() {
		
		global $app;
		
		if (isset($_GET['q'])) {
			$this->show($_GET['q']);
		} else {
			$this->add();
		}
		
	}
	
	function add() {
		
		global $app;
		
		$app->loadLayout('search/add');
		
	}
	
	function show($q) {
		
		global $app;
		
		include 'lib/search.php';
		$search = new Search;
		
		$app->page->items = $search->do_search($q);
		
		$app->loadView('partials/header');
		$app->loadView('search/add');
		$app->loadView('items/index');
		$app->loadView('partials/footer');
		
	}
	
	function json($q) {
		
		global $app;
		
		include 'lib/search.php';
		$search = new Search;
		
		$items['items'] = $search->do_search($q);
		$app->page->json = $items;
		$app->loadView('pages/json');
		
	}

}

?>