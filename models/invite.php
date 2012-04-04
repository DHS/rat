<?php

class Invite {

	// Add an invite, returns invite id
	public static function add($user_id, $email) {

		$config = new AppConfig;

		$user_id = sanitize_input($user_id);
		$email = sanitize_input($email);

		$insert_sql = "INSERT INTO `{$config->database[SITE_IDENTIFIER]['prefix']}invites` SET `user_id` = $user_id, `email` = $email";
		$insert_query = mysqli_query($insert_sql);

		$id = mysqli_insert_id();

		$update_sql = "UPDATE `{$config->database[SITE_IDENTIFIER]['prefix']}invites` SET `code` = '$id' WHERE `id` = $id";
		$query = mysqli_query($update_sql);

		return $id;

	}

	// Get a single invite, returns an Invite object
	public static function get_by_id($id) {

		$config = new AppConfig;

		$id = sanitize_input($id);

		$sql = "SELECT `id`, `user_id`, `email`, `code`, `result`, `date` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}invites` WHERE `id` = $id";
		$query = mysqli_query($sql);
		$result = mysqli_fetch_assoc($query);

		if (!is_array($result)) {
			// Invite not found

			$invite = NULL;

		} else {

			$invite = new Invite;

			foreach ($result as $k => $v) {
				$invite->$k = $v;
			}

			$invite->user = User::get_by_id($result['user_id']);

		}

		return $invite;

	}

	// Get all invites with a given code, returns an array of Invite objects
	public static function list_by_code($code, $limit = 10, $offset = 0) {

		$config = new AppConfig;

		$code = sanitize_input($code);

		$sql = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}invites` WHERE `code` = $code";

		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";

		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";

		$query = mysqli_query($sql);

		// Loop through invite ids, fetching objects
		$invites = array();
		while ($result = mysqli_fetch_assoc($query)) {
			$invites[] = Invite::get_by_id($result['id']);
		}

		return $invites;

	}

	// Update an invite
	public function update() {

		$config = new AppConfig;

		$sql_get = "SELECT `result` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}invites` WHERE `id` = $this->id";
		$query_get = mysqli_query($sql_get);
		$old_result = mysqli_result($query_get, 0);

		$new_result = $old_result + 1;

		// Update database
		$sql_update = "UPDATE `{$config->database[SITE_IDENTIFIER]['prefix']}invites` SET `result` = $new_result WHERE `id` = $this->id";
		$query_update = mysqli_query($sql_update);

	}

	// Checks to see if a user is already invited, returns TRUE or FALSE
	public static function check_invited($user_id, $email) {

		$config = new AppConfig;

		$user_id = sanitize_input($user_id);
		$email = sanitize_input($email);

		$sql = "SELECT COUNT(id) FROM `{$config->database[SITE_IDENTIFIER]['prefix']}invites` WHERE `user_id` = $user_id AND `email` = $email";
		$query = mysqli_query($sql);
		$user_count = mysqli_result($query, 0);

		if ($user_count >= 1) {

			return TRUE;

		} else {

			return FALSE;

		}

	}

	// Validates an invite code, returns TRUE or FALSE
	public static function check_code_valid($code, $email) {

		$config = new AppConfig;

		if ($code == '') {
			return FALSE;
		}

		$code = sanitize_input($code);
		$email = sanitize_input($email);

		$sql = "SELECT `result` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}invites` WHERE `code` = $code AND `email` = $email";
		$query = mysqli_query($sql);
		$status = mysqli_num_rows($query);

		if ($status > 0) {
			return TRUE;
		} else {
			return FALSE;
		}

	}

}
