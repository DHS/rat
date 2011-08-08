<?php

class friend {

	// Add a friend	
	function add($user_id, $friend_user_id) {

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
	
	// Get a users's friends
	function get($user_id) {

		global $app;

		$user_id = sanitize_input($user_id);

		$sql = "SELECT id, user_id, friend_user_id, status, date_added, date_updated FROM friends WHERE user_id = $user_id";
		$query = mysql_query($sql);

		while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$row['user'] = $app->user->get($row['friend_user_id']);
			$return[$row['id']] = $row;
		}

		return $return;

	}
	
	// Get a users's followers
	function list_followers($user_id) {

		global $app;

		$user_id = sanitize_input($user_id);

		$return = NULL;

		$sql = "SELECT id, user_id, friend_user_id, status, date_added, date_updated FROM friends WHERE friend_user_id = $user_id";
		$query = mysql_query($sql);

		while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$row['user'] = $app->user->get($row['user_id']);
			$return[$row['id']] = $row;
		}

		return $return;

	}
	
	// Update a friendship status
	function update($id, $status) {

		$id = sanitize_input($id);
		$status = sanitize_input($status);

		$sql = "UPDATE items SET status = $status WHERE id = $id";
		$query = mysql_query($sql);

	}

	// Unfriend!
	function remove($user_id, $friend_user_id) {

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
	
	// Check whether two users are friends
	function check($user_id, $friend_user_id) {

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