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

		$items = $search->do_search($q);

		foreach ($items as $item) {
			$items->content = process_content($item->content);
			foreach ($item->comments as $comment) {
				$comment->content = process_content($comment->content);
			}
			foreach ($item->likes as $like) {
			  if ($like->user_id == $_SESSION['user_id']) {
			    $item->i_like = true;
			  } else {
			    $item->i_like = false;
			  }
			}
		}

		// old template
		$this->items = $items;

		if (isset($this->plugins->log)) {
			$result_count = count($this->items);
			$this->plugins->log->add($_SESSION['user_id'], 'search', NULL, 'new', "Term = $q\nResult_count = $result_count");
		}

		if ($this->json) {
			$this->render_json($items);
		} else {
			$this->loadView('search/index', array('items' => $items));
		}

	}

}
