<?php

class user {

	// Add a user (beta signup)	
	function add($email) {

		$email = sanitize_input($email);

		$sql = "INSERT INTO users SET email = $email, date_added = NOW()";
		$query = mysql_query($sql);

		$id = mysql_insert_id();

		return $id;

	}

	// Fetch a user's info given a user_id
	function get($id) {

		$id = sanitize_input($id);

		$sql = "SELECT * FROM users WHERE id = $id";
		$query = mysql_query($sql);

		$num_rows = mysql_num_rows($query);

		if ($num_rows > 0) {

			$user = mysql_fetch_array($query, MYSQL_ASSOC);

			if ($user['full_name'] != NULL) {
				$user['name'] = $user['full_name'];
			} else {
				$user['name'] = $user['username'];
			}

		} else {

			$user = NULL;

		}

		return $user;

	}

	// Fetch a user's info given an email
	function get_by_email($email) {

		$email = sanitize_input($email);

		$query = mysql_query("SELECT * FROM users WHERE email = $email");

		if (mysql_num_rows($query) > 0) {

			$user = mysql_fetch_array($query, MYSQL_ASSOC);

			if ($user['full_name'] != NULL) {
				$user['name'] = $user['full_name'];
			} else {
				$user['name'] = $user['username'];
			}

		} else {

			$user = NULL;

		}

		return $user;

	}

	// Signup a new user!	
	function signup($user_id, $username, $password) {

		$user_id = sanitize_input($user_id);
		$username = sanitize_input($username);

		$encrypted_password = md5($password.$GLOBALS['app']['encryption_salt']);

		$sql = "UPDATE users SET username = $username, password = '$encrypted_password', date_joined = NOW() WHERE id = $user_id";
		$query = mysql_query($sql);

	}
	
	// Change password
	function update_password($user_id, $new_password) {

		$user_id = sanitize_input($user_id);

		$encrypted_password = md5($new_password.$GLOBALS['app']['encryption_salt']);

		$sql = "UPDATE users SET password = '{$encrypted_password}' WHERE id = $user_id";
		$query = mysql_query($sql);

	}

	// Update profile info
	function update_profile($user_id, $name = NULL, $bio = NULL, $url = NULL) {

		$user_id = sanitize_input($user_id);

		$sql = "UPDATE users SET ";

		if ($name != '') {
			$name = sanitize_input($name);
		} else {
			$name = 'NULL';
		}
		$sql .= "full_name = $name, ";	


		if ($bio != '') {
			$bio = sanitize_input($bio);
		} else {
			$bio = 'NULL';
		}

		$sql .= "bio = $bio, ";

		if ($url != '') {
			$url = sanitize_input($url);
		} else {
			$url = 'NULL';
		}
		$sql .= "url = $url";

		$sql .= " WHERE id = $user_id";

		$query = mysql_query($sql);

	}

	// Update a user's number of invites
	function update_invites($user_id, $invites) {

		$user_id = sanitize_input($user_id);
		$invites = sanitize_input($invites);

		// Get current # of invites
		$sql_get = "SELECT invites FROM users WHERE id = $user_id";
		$query = mysql_query($sql_get);
		$old_invites = mysql_result($query, 0);

		// Calculate new # of invites
		$new_invites = $old_invites + $invites;

		// Update database
		$sql_update = "UPDATE users SET invites = $new_invites WHERE id = $user_id";
		$query = mysql_query($sql_update);

		// update session
		if ($_SESSION['user']['id'] == $user_id) {
			$_SESSION['user']['invites'] = $new_invites;
		}

	}
	
	// Check if a username is available
	function check_username_available($username) {

		$username = sanitize_input($username);

		$query = mysql_query("SELECT COUNT(id) FROM users WHERE username = $username");
		$count = mysql_result($query, 0);

		if ($count >= 1) {

			return FALSE;

		} else {

			return TRUE;

		}

	}

	// Check if a given email already exists in the system
	function check_email_available($email) {

		$email = sanitize_input($email);

		$query = mysql_query("SELECT COUNT(id) FROM users WHERE email = $email AND date_joined IS NOT NULL");
		$user_count = mysql_result($query, 0);

		if ($user_count >= 1) {

			return FALSE;

		} else {

			return TRUE;

		}

	}

	// Check if a string (usually username) contains spaces
	function check_contains_spaces($string) {

		$array = explode(" ", $string);

		if (count($array) > 1){
			return TRUE;
		} else {
			return FALSE;
		}

	}

	// Check if a string (usually email) contains an @ symbol
	function check_contains_at($string) {

		$array = explode("@", $string);

		if (count($array) > 1){
			return TRUE;
		} else {
			return FALSE;
		}

	}

	// Check if a string (usually username) only contains only alphanumeric characters
	function check_alphanumeric($string) {

		if (ctype_alnum($string)) {

			return TRUE;

		} else {

			return FALSE;

		}

	}
	
}

?>