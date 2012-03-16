<?php

class CommentsController extends Application {

	protected $requireLoggedIn = array('add', 'remove');

	function add() {

		// Check necessary vars are present
		if (isset($_POST['item_id']) && $_POST['content'] != '') {

			// Add comment
			$comment_id = Comment::add($_SESSION['user_id'], $_POST['item_id'], $_POST['content']);

			// Log new comment
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($_SESSION['user_id'], 'comment', $comment_id, 'add', $_POST['content']);
			}

		}

		// Return comments for the relevant item
		$this->show($_POST['item_id']);

	}

	function remove($comment_id) {

		// Load relevant comment
		$comment = Comment::get_by_id($comment_id);

		// Check that comment belongs to current user
		if ($_SESSION['user_id'] == $comment->user->id) {

			// Remove comment
			$comment->remove();

			// Log comment removal
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($_SESSION['user_id'], 'comment', $comment->id, 'remove');
			}

		}

		// Return comments view
		$this->show($comment->item_id);

	}

	private function show($item_id) {

		$item = Item::get_by_id($item_id);
		$item->content = process_content($item->content);
		foreach ($item->comments as $comment) {
			$comment->content = process_content($comment->content);
		}

		if ($this->config->theme == 'twig') {

			// Copying the work of loadView
			$params = array(	'app'		=> $this,
								'session'	=> $_SESSION
							);

			$params['item'] = $item;

			echo $this->twig->render("partials/comments.html", $params);

		} else {

			// old template
			$this->item = $item;
			$this->show_comment_form = TRUE;
			$this->loadPartial('comments');

		}

	}

	function json($item_id) {

		$item = Item::get_by_id($item_id);
		$this->json = $item->comments;
		$this->loadView('pages/json', NULL, 'none');

	}

}

?>