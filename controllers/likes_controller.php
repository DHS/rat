<?php

class LikesController {
	
	function add($item_id) {
		
		global $app;
		
		$like_id = $app->like->add($_SESSION['user']['id'], $item_id);

		if (isset($app->plugins->log))
			$app->plugins->log->add($_SESSION['user']['id'], 'like', $like_id, 'add');

		$this->show($item_id);
		
	}
	
	function remove($item_id) {
		
		global $app;
		
		$like_id = $app->like->remove($_SESSION['user']['id'], $item_id);

		if (isset($app->plugins->log))
			$app->plugins->log->add($_SESSION['user']['id'], 'like', $like_id, 'remove');

		$this->show($item_id);
		
	}
	
	function show($item_id) {
		
		global $app;
		
		$app->page->item = $app->item->get($item_id);
		$app->loadView('likes/index');
		
	}
	
	function json($item_id) {
		
		global $app;
		
		$item = $app->item->get($item_id);
		$app->page->json = $item['likes'];
		$app->loadView('pages/json');
		
	}
	
}
