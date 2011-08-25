<?php

class Like {

	// Add a like for an item, returns id
	public static function add($user_id, $item_id) {
		
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

	// Get all liked items, returns an array of Like objects
	public static function list_all($limit = 10) {
		
		$sql = "SELECT * FROM likes ORDER BY date DESC";

		// Limit not null so create limit string
		if ($limit != NULL) {
			$sql .= " LIMIT $limit";
			$limit = sanitize_input($limit);
		}

		// Get likes
		$query = mysql_query($sql);

		// Loop through likes
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			
			$like = new Like;
			
			foreach($result as $k => $v) {
				$like->$k = $v;
			}
			
			// Get info about the liker
			$like->user = User::get_by_id($like->user_id);
			
			// Get item info
			$like->item = Item::get($like->user_id);
			
			$likes[] = $like;
			
		}
		
		return $likes;

	}

	// Unlike an item, returns like id
	public static function remove($user_id, $item_id) {

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
