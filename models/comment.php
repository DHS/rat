<?php

class comment {
	
	// Add a comment to an item
	function add($user_id, $item_id, $content) {
		
		$user_id = sanitize_input($user_id);
		$item_id = sanitize_input($item_id);
		$content = sanitize_input($content);

		$sql = "INSERT INTO comments SET user_id = $user_id, item_id = $item_id, content = $content";
		$query = mysql_query($sql);

		$id = mysql_insert_id();

		return $id;

	}
	
	// Get comments for an item
	function list_item($id) {
		
		$id = sanitize_input($id);

		$sql = "SELECT id, content, user_id, date FROM comments WHERE item_id = $id ORDER BY id ASC";
		$query = mysql_query($sql);

		while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$row['user'] = $user->get($row['user_id']);
			$return[] = $row;
		}

		return $return;

	}
	
	// Remove a comment from an item
	function remove($user_id, $item_id, $comment_id) {

		$user_id = sanitize_input($user_id);
		$item_id = sanitize_input($item_id);
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