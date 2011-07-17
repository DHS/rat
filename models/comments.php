<?php

function comments_get($id) {
	// Get comments for an item
	
	$id = sanitize_input($id);
	
	$sql = "SELECT id, content, user_id, date FROM comments WHERE item_id = $id ORDER BY id ASC";
	$query = mysql_query($sql);
	
	while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
		$row['user'] = user_get_by_id($row['user_id']);
		$return[] = $row;
	}
	
	return $return;
	
}

function comments_add($user_id, $item_id, $content) {
	// Add a comment to an item
	
	$user_id = sanitize_input($user_id);
	$item_id = sanitize_input($item_id);
	$content = sanitize_input($content);
	
	$sql = "INSERT INTO comments SET user_id = $user_id, item_id = $item_id, content = $content";
	$query = mysql_query($sql);

	$id = mysql_insert_id();
	
	return $id;

}

function comments_remove($user_id, $item_id, $comment_id) {
	// Remove a comment from an item
	
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

function comments_likes_get($item_id) {
	// Get likes for an item
	
	$item_id = sanitize_input($item_id);
	
	$sql = "SELECT id, user_id, date FROM comment_likes WHERE item_id = $item_id";
	$query = mysql_query($sql);
	
	while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
		$row['user'] = user_get_by_id($row['user_id']);
		$return[$row['id']] = $row;
	}
	
	return $return;
	
}

function comments_likes_add($user_id, $comment_id) {
	// Add a like for an item
	
	$user_id = sanitize_input($user_id);
	$comment_id = sanitize_input($comment_id);
	
	$count_sql = "SELECT id FROM comment_likes WHERE user_id = $user_id AND comment_id = $comment_id";
	$count_query = mysql_query($count_sql);
	
	if (mysql_num_rows($count_query) < 1) {
		$sql = "INSERT INTO comment_likes SET user_id = $user_id, comment_id = $comment_id";
		$query = mysql_query($sql);
	}
	
	$id = mysql_insert_id();
	
	return $id;

}

function comments_likes_remove($user_id, $comment_id) {
	// Unlike an item
	
	$user_id = sanitize_input($user_id);
	$comment_id = sanitize_input($comment_id);
	
	$count_sql = "SELECT id FROM comment_likes WHERE user_id = $user_id AND comment_id = $comment_id";
	$count_query = mysql_query($count_sql);
	
	$id = mysql_result($count_query, 0);
	
	if (mysql_num_rows($count_query) > 0) {
		$sql = "DELETE FROM comment_likes WHERE user_id = $user_id AND comment_id = $comment_id";
		$query = mysql_query($sql);
	}
	
	return $id;

}

?>