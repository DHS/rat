<?php

class LikesController {
	
	function add($user_id, $item_id) {
		
		global $app;
		
		$this->auth_check($user_id);
		
		$like_id = $app->like->add($user_id, $item_id);

		if (isset($app->plugins->log))
			$app->plugins->log->add($user_id, 'like', $like_id, 'add');

		$this->show($item_id);
		
	}
	
	function remove($user_id, $item_id) {
		
		global $app;
		
		$this->auth_check($user_id);
		
		$like_id = $app->like->remove($user_id, $item_id);

		if (isset($app->plugins->log))
			$app->plugins->log->add($user_id, 'like', $like_id, 'remove');

		$this->show($item_id);
		
	}
	
	function show($item_id) {
		
		global $app;
		
		$app->page->item = $app->item->get($item_id);
		$app->loadView('likes');
		
	}
	
	function json($item_id) {
		
		global $app;
		
		$item = $app->item->get($item_id);
		$app->page->json = $item['likes'];
		$app->loadView('pages/json');
		
	}
	
	function auth_check($user_id) {
		
		if ($user_id != $_SESSION['user']['id'])
			exit();
		
	}
	
}

