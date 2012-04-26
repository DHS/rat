<?php

class Like {

	public function __construct(array $attrs = null) {

		if (is_array($attrs)) {
			foreach ($attrs as $key => $value) {
				$this->$key = $value;
			}
		}

	}

	// Add a like for an item, returns id
	public static function add($user_id, $item_id) {

		$config = new AppConfig;

		$user_id = sanitize_input($user_id);
		$item_id = sanitize_input($item_id);

		$count_sql = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}likes` WHERE `user_id` = $user_id AND `item_id` = $item_id";
		$count_query = mysql_query($count_sql);

		if (mysql_num_rows($count_query) < 1) {
			$sql = "INSERT INTO `{$config->database[SITE_IDENTIFIER]['prefix']}likes` SET `user_id` = $user_id, `item_id` = $item_id";
			$query = mysql_query($sql);
		}

		return mysql_insert_id();

	}

	// Get a single like, returns a Like object
	public static function get_by_id($id) {

		$config = new AppConfig;

		$id = sanitize_input($id);

		$sql = "SELECT `id`, `user_id`, `item_id`, `date` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}likes` WHERE `id` = $id";
		$query = mysql_query($sql);
		$result = mysql_fetch_array($query, MYSQL_ASSOC);

		if (!is_array($result)) {

			return null;

		} else {

			$like = new Like($result);
			$like->user = User::get_by_id($result['user_id']);
			return $like;

		}

	}

	// Get a single like, returns a Like object
	public static function get_by_user_item($user_id, $item_id) {

		$config = new AppConfig;

		$user_id = sanitize_input($user_id);
		$item_id = sanitize_input($item_id);

		$sql = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}likes` WHERE `user_id` = $user_id AND `item_id` = $item_id";
		$query = mysql_query($sql);
		$result = mysql_result($query, 0);

		if ($result == FALSE) {

			return $null;

		} else {

			$like = Like::get_by_id($result);
			$like->user = User::get_by_id($user_id);
			return $like;

		}

	}

	// Get all liked items, returns an array of Like objects
	public static function list_all($limit = 10, $offset = 0) {

		$config = new AppConfig;

		$sql = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}likes` ORDER BY `date` DESC";

		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";

		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";

		// Get likes
		$query = mysql_query($sql);

		// Loop through likes, fetching objects
		$likes = array();
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$likes[] = Like::get_by_id($result['id']);
		}

		return $likes;

	}

	// Unlike an item, returns like id
	public function remove() {

		$config = new AppConfig;

		$count_sql = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}likes` WHERE `id` = $this->id";
		$count_query = mysql_query($count_sql);

		if (mysql_num_rows($count_query) > 0) {
			$sql = "DELETE FROM `{$config->database[SITE_IDENTIFIER]['prefix']}likes` WHERE `id` = $this->id";
			$query = mysql_query($sql);
		}

	}

}
