<?php

class SearchController extends Application {
	
	function index() {
		
		if (isset($this->uri['params']['q'])) {
			$this->show($this->uri['params']['q']);
		} else {
			$this->add();
		}
		
	}
	
	function add() {
		
		$this->loadView('search/add');
		
	}
	
	private function show($q) {
		
		include 'lib/search.php';
		$search = new Search;
		
		$this->items = $search->do_search($q);
		
		foreach ($items as $key => $item) {
			$items[$key]->content = process_content($items[$key]->content);
		}
		
		if (isset($this->plugins->log)) {
			$result_count = count($this->items);
			$this->plugins->log->add($_SESSION['user_id'], 'search', NULL, 'new', "Term = $q\nResult_count = $result_count");
		}
		
		if ($this->json) {
			$this->render_json($this->items);
		} else {
			$this->loadView('search/index');
		}
		
	}

}

?>