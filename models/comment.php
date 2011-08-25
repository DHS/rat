<?php

class Comment {
	
	// Add a comment to an item, returns comment id
	public static function add($user_id, $item_id, $content) {
		
		$user_id = sanitize_input($user_id);
		$item_id = sanitize_input($item_id);
		$content = sanitize_input($content);

		$sql = "INSERT INTO comments SET user_id = $user_id, item_id = $item_id, content = $content";
		$query = mysql_query($sql);

		$id = mysql_insert_id();

		return $id;

	}
	
	// Get a single comment, returns a Comment object
	public static function get($id) {
		
		$id = sanitize_input($id);
        
		$sql = "SELECT * FROM comments WHERE id = $id";
		$query = mysql_query($sql);
		$result = mysql_fetch_array($query, MYSQL_ASSOC);

		if (!is_array($result)) {

			$comment = NULL;

		} else {
		
			$comment = new Comment;
			
			foreach ($result as $k => $v) {
				$comment->$k = $v;
			}
			
			$comment->user = User::get_by_id($result['user_id']);
			
		}
        
		return $comment;
		
	}
	
	// Remove a comment from an item, returns comment id
	public static function remove($user_id, $comment_id) {

		$user_id = sanitize_input($user_id);
		$comment_id = sanitize_input($comment_id);

		$count_sql = "SELECT id FROM comments WHERE user_id = $user_id AND id = $comment_id";
		$count_query = mysql_query($count_sql);
		$id = mysql_result($count_query, 0);

		if (mysql_num_rows($count_query) > 0) {
			$sql = "DELETE FROM comments WHERE user_id = $user_id AND id = $comment_id";
			$query = mysql_query($sql);
		}

		return $id;

	}
	
}

?>