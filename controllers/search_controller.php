<?php

class SearchController extends Application {
	
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
		
		$this->loadLayout('search/add');
		
	}
	
	function show($q) {
		
		global $app;
		
		include 'lib/search.php';
		$search = new Search;
		
		$page['items'] = $search->do_search($q);
		
<<<<<<< HEAD
		$app->page->name = 'Search';
		$app->loadView('partials/header');
		$app->loadView('search/add');
		$app->loadView('items/index');
		$app->loadView('partials/footer');
=======
		$this->loadView('partials/header');
		$this->loadView('search/add');
		$this->loadView('items/index');
		$this->loadView('partials/footer');
>>>>>>> new-models
		
	}
	
	function json($q) {
		
		global $app;
		
		include 'lib/search.php';
		$search = new Search;
		
		$items['items'] = $search->do_search($q);
		$page['json'] = $items;
		$this->loadView('pages/json');
		
	}

}

?>