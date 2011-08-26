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
		
		$this->loadView('search/add');
		
	}
	
	function show($q) {
		
		include 'lib/search.php';
		$search = new Search;
		
		$this->items = $search->do_search($q);
		
		if (isset($this->plugins->log)) {
			$result_count = count($this->items);
			$this->plugins->log->add($_SESSION['user']['id'], 'search', NULL, 'new', "Term = $q\nResult_count = $result_count");
		}
		
		$this->loadView('search/index');
		
	}
	
	function json($q) {
		
		include 'lib/search.php';
		$search = new Search;
		
		$items['items'] = $search->do_search($q);
		$this->json = $items;
		$this->loadView('pages/json', 'none');
		
	}

}

?>