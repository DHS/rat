<?php

class like {

	// Add a like for an item
	function add($user_id, $item_id) {
		
		$user_id = sanitize_input($user_id);
		$item_id = sanitize_input($item_id);

		$count_sql = "SELECT id FROM likes WHERE user_id = $user_id AND item_id = $item_id";
		$count_query = mysql_query($count_sql);

		if (mysql_num_rows($count_query) < 1) {
			$sql = "INSERT INTO likes SET user_id = $user_id, item_id = $item_id";
			$query = mysql_query($sql);
		}

		$id = mysql_insert_id();

		return $id;

	}

	// Get likes for an item
	function get($item_id) {

		$item_id = sanitize_input($item_id);

		$sql = "SELECT id, user_id, date FROM likes WHERE item_id = $item_id";
		$query = mysql_query($sql);

		while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$row['user'] = $user->get($row['user_id']);
			$return[$row['id']] = $row;
		}

		return $return;

	}

	// Get all liked items
	function list_all($limit = 10) {
		
		$sql = "SELECT * FROM likes ORDER BY date DESC";

		// Limit not null so create limit string
		if ($limit != NULL) {
			$sql .= " LIMIT $limit";
			$limit = sanitize_input($limit);
		}

		// Get likes
		$query = mysql_query($sql);

		// Loop through likes
		while ($like = mysql_fetch_array($query, MYSQL_ASSOC)) {

			// Get info about the liker
			$item['user'] = $user->get($like['user_id']);

			// Get item info
			$sql2 = "SELECT * FROM items WHERE id = {$like['item_id']} LIMIT 1";
			$query2 = mysql_query($sql2);
			$item['item'] = mysql_fetch_array($query2, MYSQL_ASSOC);

			// Get info on user who created the item
			$item['item']['user'] = $user->get($item['item']['user_id']);

			$items[] = $item;

		}

		return $items;

	}

	// Unlike an item
	function remove($user_id, $item_id) {

		$user_id = sanitize_input($user_id);
		$item_id = sanitize_input($item_id);

		$count_sql = "SELECT id FROM likes WHERE user_id = $user_id AND item_id = $item_id";
		$count_query = mysql_query($count_sql);

		$id = mysql_result($count_query, 0);

		if (mysql_num_rows($count_query) > 0) {
			$sql = "DELETE FROM likes WHERE user_id = $user_id AND item_id = $item_id";
			$query = mysql_query($sql);
		}

		return $id;

	}

}

?>