<?php

class LikesController extends Application {
	
	function add($item_id) {
		
		$like_id = Like::add($_SESSION['user']['id'], $item_id);

		if (isset($this->plugins->log))
			$this->plugins->log->add($_SESSION['user']['id'], 'like', $like_id, 'add');

		$this->show($item_id);
		
	}
	
	function remove($item_id) {
		
		$like_id = Like::remove($_SESSION['user']['id'], $item_id);

		if (isset($this->plugins->log))
			$this->plugins->log->add($_SESSION['user']['id'], 'like', $like_id, 'remove');

		$this->show($item_id);
		
	}
	
	function show($item_id) {
		
		$this->item = Item::get($item_id);
		$this->loadView('likes/index');
		
	}
	
	function json($item_id) {
		
		$item = Item::get($item_id);
		$this->json = $item->likes;
		$this->loadView('pages/json');
		
	}
	
}
