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
        
		$sql = "SELECT id, user_id, item_id, content, date FROM comments WHERE id = $id";
		$query = mysql_query($sql);
		$result = mysql_fetch_array($query, MYSQL_ASSOC);
		
		if (!is_array($result)) {
			// Comment not found
			
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
	public static function list_all($limit = 10, $offset = 0) {
		
		$sql = "SELECT id FROM comments ORDER BY date DESC";
		
		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";
		
		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";
		
		// Get list of ids
		$query = mysql_query($sql);
		
		// Loop through comment ids, fetching objects
		$comments = array();
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$comments[] = Comment::get_by_id($result['id']);
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