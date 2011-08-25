<?php

class Friend {

	// Add a friend, returns friendship id
	public static function add($user_id, $friend_user_id) {

		$user_id = sanitize_input($user_id);
		$friend_user_id = sanitize_input($friend_user_id);

		$count_sql = "SELECT id FROM friends WHERE user_id = $user_id AND friend_user_id = $friend_user_id";
		$count_query = mysql_query($count_sql);

		if (mysql_num_rows($count_query) < 1) {
			$sql = "INSERT INTO friends SET user_id = $user_id, friend_user_id = $friend_user_id";
			$query = mysql_query($sql);
		}

		$id = mysql_insert_id();

		return $id;

	}
	
	// Update a friendship status
	public static function update($id, $status) {

		$id = sanitize_input($id);
		$status = sanitize_input($status);

		$sql = "UPDATE items SET status = $status WHERE id = $id";
		$query = mysql_query($sql);

	}

	// Unfriend! Returns friendship id
	public static function remove($user_id, $friend_user_id) {

		$user_id = sanitize_input($user_id);
		$friend_user_id = sanitize_input($friend_user_id);

		$count_sql = "SELECT id FROM friends WHERE user_id = $user_id AND friend_user_id = $friend_user_id";
		$count_query = mysql_query($count_sql);

		$id = mysql_result($count_query, 0);

		if (mysql_num_rows($count_query) > 0) {
			$sql = "DELETE FROM friends WHERE user_id = $user_id AND friend_user_id = $friend_user_id";
			$query = mysql_query($sql);
		}

		return $id;

	}
	
	// Check whether two users are friends, returns TRUE or FALSE
	public static function check($user_id, $friend_user_id) {

		$user_id = sanitize_input($user_id);
		$friend_user_id = sanitize_input($friend_user_id);

		$count_sql = "SELECT COUNT(id) FROM friends WHERE user_id = $user_id AND friend_user_id = $friend_user_id";
		$count_query = mysql_query($count_sql);
		$count = mysql_result($count_query, 0);

		if ($count > 0) {
			return TRUE;
		} else {
			return FALSE;
		}

	}
	
}

?>
