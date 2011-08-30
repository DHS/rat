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
	public static function get_by_id($id) {
		
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
	
	// Get all comments, returns an array of Comments objects
	public static function list_all($limit = 10) {
		
		$sql = "SELECT * FROM comments ORDER BY date DESC";
		
		// Limit not null so create limit string
		if ($limit != NULL) {
			$sql .= " LIMIT $limit";
			$limit = sanitize_input($limit);
		}
		
		// Get comments
		$query = mysql_query($sql);
		
		// Loop through comments
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			
			$comment = new Comment;
			
			foreach($result as $k => $v) {
				$comment->$k = $v;
			}
			
			// Get info about the commenter
			$comment->user = User::get_by_id($comment->user_id);
			
			// Get item info
			$comment->item = Item::get_by_id($comment->user_id);
			
			$comments[] = $comment;
			
		}
		
		return $comments;
		
	}
	
	// Remove a comment from an item, returns comment id
	public function remove() {
		
		$count_sql = "SELECT id FROM comments WHERE id = {$this->id}";
		$count_query = mysql_query($count_sql);
		
		if (mysql_num_rows($count_query) > 0) {
			$sql = "DELETE FROM comments WHERE id = {$this->id}";
			$query = mysql_query($sql);
		}
		
		return $this->id;
		
	}
	
}

?>