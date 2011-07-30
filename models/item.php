<?php

class item {

	// Create an item	
	function add($user_id, $content, $title = NULL, $image = NULL) {

		$user_id = sanitize_input($user_id);
		$content = sanitize_input($content);

		$sql = "INSERT INTO items SET user_id = $user_id, content = $content";

		if ($title != NULL) {
			$title = sanitize_input($title);
			$sql .= ", title = $title";
		}

		if ($image != NULL) {
			$image = sanitize_input($image);
			$sql .= ", image = $image";
		}

		$query = mysql_query($sql);

		$id = mysql_insert_id();

		return $id;

	}

	// Get an item by id	
	function get($id) {

		$id = sanitize_input($id);

		$sql = "SELECT * FROM items WHERE id = $id ORDER BY id DESC";
		$query = mysql_query($sql);
		$item = mysql_fetch_array($query, MYSQL_ASSOC);

		if (!is_array($item)) {

			$item = NULL;

		} else {

			$item['user'] = user_get_by_id($item['user_id']);
			$item['comments'] = comments_get($id);
			$item['likes'] = likes_get($id);

		}

		return $item;

	}

	// Get recent items
	function list_all($limit = 20) {

		$sql = "SELECT * FROM items ORDER BY id DESC";

		// Limit not null so create limit string
		if ($limit != NULL) {
			$limit = sanitize_input($limit);
			$sql .= " LIMIT $limit";
		}

		$query = mysql_query($sql);

		while ($item = mysql_fetch_array($query, MYSQL_ASSOC)) {

			$item['comments'] = comments_get($item['id']);
			$item['likes'] = likes_get($item['id']);
			$item['user'] = user_get_by_id($item['user_id']);

			$items[] = $item;

		}

		return $items;

	}

	// Get a user's items
	function list_user($user_id, $limit = 10) {

		$user_id = sanitize_input($user_id);

		$sql = "SELECT * FROM items WHERE user_id = $user_id ORDER BY id DESC";

		// Limit not null so create limit string
		if ($limit != NULL) {
			$limit = sanitize_input($limit);
			$sql .= " LIMIT $limit";
		}

		$query = mysql_query($sql);

		while ($item = mysql_fetch_array($query, MYSQL_ASSOC)) {

			$item['user'] = user_get_by_id($item['user_id']);
			$item['comments'] = comments_get($item['id']);
			$item['likes'] = likes_get($item['id']);

			$items[] = $item;

		}

		return $items;

	}

	// Get items liked by a user
	function list_user_liked($user_id, $limit = 10) {

		$user_id = sanitize_input($user_id);

		$sql = "SELECT item_id FROM likes WHERE user_id = $user_id AND status = 1 ORDER BY date DESC";

		// Limit not null so create limit string
		if ($limit != NULL) {
			$limit = sanitize_input($limit);
			$sql .= " LIMIT $limit";
		}

		$query = mysql_query($sql);
		$count = mysql_num_rows($query);

		while ($item = mysql_fetch_array($query, MYSQL_ASSOC)) {

			$query2 = mysql_query("SELECT * FROM items WHERE id = {$item['item_id']} LIMIT 1");
			$item = mysql_fetch_array($query2, MYSQL_ASSOC);

			$item['user'] = user_get_by_id($item['user_id']);
			$item['comments'] = comments_get($item['id']);
			$item['likes'] = likes_get($item['id']);

			$items[] = $item;

		}

		return $items;

	}
	
	// Get a feed of a friend's activity
	function list_feed($user_id) {

		// Start by adding the viewer to the query string
		$friends_string  = "user_id = $user_id";

		// Fetch friends
		$friends = friends_get($user_id);

		// Loop through friends adding them to the query string
		foreach ($friends as $friend)
			$friends_string .= " OR user_id = {$friend['friend_user_id']}";

		$sql = "SELECT * FROM items WHERE $friends_string ORDER BY id DESC LIMIT 100";
		$query = mysql_query($sql);

		while ($item = mysql_fetch_array($query, MYSQL_ASSOC)) {

			$item['comments'] = comments_get($item['id']);
			$item['likes'] = likes_get($item['id']);
			$item['user'] = user_get_by_id($item['user_id']);

			$items[] = $item;

		}

		return $items;

	}

	// Remove an item
	function remove($item_id) {

		$item_id = sanitize_input($item_id);

		// Check item exists
		$sql_check = "SELECT id FROM items WHERE id = $item_id";
		$count_query = mysql_query($sql_check);
		$id = mysql_result($count_query, 0);


		if (mysql_num_rows($count_query) > 0) {
			// If item exists, go ahead and delete
			$sql_delete = "DELETE FROM items WHERE id = $item_id";
			$query = mysql_query($sql_delete);
		}

		return $id;

	}

}

?>