<?php

class SearchController extends Application {
	
	function index() {
		
		if (isset($_GET['q'])) {
			$this->show($_GET['q']);
		} else {
			$this->add();
		}
		
	}
	
	function add() {
		
		$this->loadLayout('search/add');
		
	}
	
	function show($q) {
		
		include 'lib/search.php';
		$search = new Search;
		
		$this->page['items'] = $search->do_search($q);

		$this->loadView('partials/header');
		$this->loadView('search/add');
		$this->loadView('items/index');
		$this->loadView('partials/footer');
		
	}
	
	function json($q) {
		
		include 'lib/search.php';
		$search = new Search;
		
		$items['items'] = $search->do_search($q);
		$this->page['json'] = $items;
		$this->loadView('pages/json');
		
	}

}

?>