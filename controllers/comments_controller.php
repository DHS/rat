<?php

class CommentsController {
	
	function add($user_id, $item_id, $content) {
		
		global $app;
		
		$this->auth_check($user_id);
		
		$comment_id = $app->comment->add($user_id, $item_id, $content);
		
		if (isset($app->plugins->log))
			$app->plugins->log->add($user_id, 'comment', $comment_id, 'add', $content);
		
		$this->show($item_id);
		
	}
	
	function remove($user_id, $item_id, $comment_id) {
		
		global $app;
		
		$this->auth_check($user_id);
		
		$app->comment->remove($user_id, $comment_id);
		
		if (isset($app->plugins->log))
			$app->plugins->log->add($user_id, 'comment', $comment_id, 'remove');
		
		$this->show($item_id);
		
	}
	
	function show($item_id) {
		
		global $app;
		
		$app->page->item = $app->item->get($item_id);
		$app->loadView('comments/index');
		
	}
	
	function json($item_id) {
		
		global $app;
		
		$item = $app->item->get($item_id);
		$app->page->json = $item['comments'];		
		$app->loadView('pages/json');
		
	}
	
	function auth_check($user_id) {
		
		if ($user_id != $_SESSION['user']['id'])
			exit();
		
	}
	
}

