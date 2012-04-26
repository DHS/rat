<?php

class User {

	public function __construct(array $attrs = null) {

		if (is_array($attrs)) {
			foreach ($attrs as $key => $value) {
				$this->$key = $value;
			}
		}

	}

	// Add a user (beta signup)
	public static function add($email) {

		$config = new AppConfig;

		$email = sanitize_input($email);

		$sql = "INSERT INTO `{$config->database[SITE_IDENTIFIER]['prefix']}users` SET `email` = $email, `date_added` = NOW()";
		$query = mysqli_query($sql);

		return mysqli_insert_id();

	}

	// Fetch a user's info given a user_id
	public static function get_by_id($id) {

		$config = new AppConfig;

		$id = sanitize_input($id);

		$sql = "SELECT `id`, `username`, `email`, `full_name`, `bio`, `url`, `points`, `invites`, `password`, `date_added`, `date_joined` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}users` WHERE `id` = $id";
		$query = mysqli_query($sql);
		$result = mysqli_fetch_assoc($query);

		if ( ! is_array($result)) {

			return null;

		} else {

			return new User($result);

		}

	}

	// Fetch a user's info given an email
	public static function get_by_username($username) {

		$config = new AppConfig;

		$username = sanitize_input($username);

		$sql = "SELECT `id`, `username`, `email`, `full_name`, `bio`, `url`, `points`, `invites`, `password`, `date_added`, `date_joined` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}users` WHERE `username` = $username";
		$query = mysqli_query($sql);
		$result = mysqli_fetch_assoc($query);

		if ( ! is_array($result)) {

			return null;

		} else {

			return new User($result);

		}

	}

	// Fetch a user's info given an email
	public static function get_by_email($email) {

		$config = new AppConfig;

		$email = sanitize_input($email);

		$sql = "SELECT `id`, `username`, `email`, `full_name`, `bio`, `url`, `points`, `invites`, `password`, `date_added`, `date_joined` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}users` WHERE `email` = $email";
		$query = mysqli_query($sql);
		$result = mysqli_fetch_assoc($query);

		if ( ! is_array($result)) {

			return null;

		} else {

			return new User($result);

		}

	}

	// Signup a new user!
	public static function signup($id, $username, $password, $salt) {

		$config = new AppConfig;

		$id = sanitize_input($id);
		$username = sanitize_input($username);

		$encrypted_password = md5($password . $salt);

		$sql = "UPDATE `{$config->database[SITE_IDENTIFIER]['prefix']}users` SET `username` = $username, `password` = '$encrypted_password', `date_joined` = NOW() WHERE `id` = $id";
		$query = mysqli_query($sql);

	}

	// Check user's login details
	public function authenticate($new_password, $salt) {

		if ($this->password == md5($new_password . $salt)) {

			// Update session
			$_SESSION['user_id'] = $this->id;

			// Log login
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($_SESSION['user_id'], 'user', NULL, 'login');
			}

			return true;

		} else {

			return false;

		}

	}

	// Log a user out
	public function deauthenticate() {

		if (isset($_SESSION['user_id'])) {

			session_unset();
			session_destroy();

			// Log logout
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($this->id, 'user', NULL, 'logout');
			}

			return true;

		} else {

			return false;

		}

	}

	// Remove a user. WTF?
	public static function remove() {

		$config = new AppConfig;

		// Check item exists
		$sql_check = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}users` WHERE `id` = $this->id";
		$count_query = mysqli_query($sql_check);

		if (mysqli_num_rows($count_query) > 0) {
			// If user exists, go ahead and delete
			$sql_delete = "DELETE FROM `user` WHERE `id` = $this->id";
			$query = mysqli_query($sql_delete);
		}

	}

	// Get a user's items, returns array of Item objects
	public function items($limit = 10, $offset = 0) {

		$config = new AppConfig;

		$sql = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}items` WHERE `user_id` = $this->id ORDER BY `id` DESC";

		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";

		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";

		$query = mysqli_query($sql);

		$items = array();
		while ($result = mysqli_fetch_assoc($query)) {
			$items[] = Item::get_by_id($result['id']);
		}

		return $items;

	}

	// Get all invites sent by a user, returns an array of Invite objects
	public function invites($limit = 10, $offset = 0) {

		$config = new AppConfig;

		$sql = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}invites` WHERE `user_id` = $this->id ORDER BY `id` DESC";

		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";

		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";

		$query = mysqli_query($sql);

		$invites = array();
		while ($result = mysqli_fetch_assoc($query)) {
			$invites[] = Invite::get_by_id($result['id']);
		}

		return $invites;

	}

	// Get a users's friends, returns a list of User items
	public function friends($limit = 10, $offset = 0) {

		$config = new AppConfig;

		$sql = "SELECT `id`, `friend_user_id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}friends` WHERE `user_id` = $this->id";

		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";

		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";

		$query = mysqli_query($sql);

		$friends = array();
		while ($result = mysqli_fetch_assoc($query)) {
			$friends[$result['id']] = User::get_by_id($result['friend_user_id']);
		}

		return $friends;

	}

	// Get a users's followers, returns a list of Friend items
	public function followers($limit = 10, $offset = 0) {

		$config = new AppConfig;

		$sql = "SELECT `id`, `friend_user_id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}friends` WHERE `friend_user_id` = $this->id";

		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";

		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";

		$query = mysqli_query($sql);

		$friends = array();
		while ($result = mysqli_fetch_assoc($query)) {
			$friends[$result['id']] = User::get_by_id($result['user_id']);
		}

		return $friends;

	}

	// Get items liked by a user, returns array of Item objects
	public function likes($limit = 10, $offset = 0) {

		$config = new AppConfig;

		$sql = "SELECT `item_id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}likes` WHERE `user_id` = $this->id ORDER BY `date` DESC";

		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";

		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";

		$query = mysqli_query($sql);

		$items = array();
		while ($result = mysqli_fetch_assoc($query)) {
			$items[] = Item::get_by_id($result['item_id']);
		}

		return $items;

	}

	// Get comments made by a user, returns an array of Comment objects
	public function comments($limit = 10, $offset = 0) {

		$config = new AppConfig;

		$sql = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}comments` WHERE `user_id` = $this->id ORDER BY `id` ASC";

		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";

		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";

		$query = mysqli_query($sql);

		$comments = array();
		while ($result = mysqli_fetch_assoc($query)) {
			$comments[] = Comment::get_by_id($result['id']);
		}

		return $comments;

	}

	// Add a friend, returns friendship id
	public function friend_add($friend_user_id) {

		$config = new AppConfig;

		$friend_user_id = sanitize_input($friend_user_id);

		$count_sql = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}friends` WHERE `user_id` = {$this->id} AND `friend_user_id` = $friend_user_id";
		$count_query = mysqli_query($count_sql);

		if (mysqli_num_rows($count_query) < 1) {
			$sql = "INSERT INTO `{$config->database[SITE_IDENTIFIER]['prefix']}friends` SET `user_id` = {$this->id}, `friend_user_id` = $friend_user_id";
			$query = mysqli_query($sql);
		}

	}

	// Update a friendship status
	public function friend_update($friend_user_id, $status) {

		$config = new AppConfig;

		$friend_user_id = sanitize_input($friend_user_id);
		$status = sanitize_input($status);

		$sql = "UPDATE `{$config->database[SITE_IDENTIFIER]['prefix']}friends` SET `status` = $status WHERE `user_id` = {$this->id} AND `friend_user_id` = {$friend_user_id}";
		$query = mysqli_query($sql);

	}

	// Unfriend! Returns friendship id
	public function friend_remove($friend_user_id) {

		$config = new AppConfig;

		$friend_user_id = sanitize_input($friend_user_id);

		$count_sql = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}friends` WHERE `user_id` = {$this->id} AND `friend_user_id` = {$friend_user_id}";
		$count_query = mysqli_query($count_sql);

		if (mysqli_num_rows($count_query) > 0) {
			$sql = "DELETE FROM `{$config->database[SITE_IDENTIFIER]['prefix']}friends` WHERE `user_id` = {$this->id} AND `friend_user_id` = {$friend_user_id}";
			$query = mysqli_query($sql);
		}

	}

	// Get a feed of a friend's activity, returns array of Item objects
	public function list_feed($limit = 10, $offset = 0) {

		$config = new AppConfig;

		// Start by adding the viewer to the query string
		$friends_string = "`user_id` = {$this->id}";

		$friends = $this->friends();

		// Loop through friends adding them to the query string
		foreach ($friends as $friend) {
			$friends_string .= " OR `user_id` = {$friend['friend_user_id']}";
		}

		$sql = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}items` WHERE $friends_string ORDER BY `id` DESC";

		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";

		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";

		$query = mysqli_query($sql);

		// Loop through item ids, fetching objects
		$items = array();
		while ($result = mysqli_fetch_assoc($query)) {
			$items[] = Item::get_by_id($result['id']);
		}

		return $items;

	}

	// Check whether two users are friends, returns TRUE or FALSE
	public function friend_check($friend_user_id) {

		$config = new AppConfig;

		$friend_user_id = sanitize_input($friend_user_id);

		$sql = "SELECT COUNT(id) FROM `{$config->database[SITE_IDENTIFIER]['prefix']}friends` WHERE `user_id` = $friend_user_id AND `friend_user_id` = {$this->id}";
		$query = mysqli_query($sql);
		$result = mysqli_result($query, 0);

		if ($result > 0) {
			return true;
		} else {
			return false;
		}

	}

	public function is_admin(array $admin_users = array()) {
		return in_array($this->id, $admin_users);
	}

	// Change password
	public function update_password($new_password, $salt) {

		$config = new AppConfig;

		$encrypted_password = md5($new_password . $salt);

		$sql = "UPDATE `{$config->database[SITE_IDENTIFIER]['prefix']}users` SET `password` = '{$encrypted_password}' WHERE `id` = $this->id";
		$query = mysqli_query($sql);

	}

	// Update profile info
	public function update_profile($name = NULL, $bio = NULL, $url = NULL) {

		$config = new AppConfig;

		$sql = "UPDATE `{$config->database[SITE_IDENTIFIER]['prefix']}users` SET ";

		if ($name != '') {
			$name = sanitize_input($name);
		} else {
			$name = 'NULL';
		}
		$sql .= "`full_name` = $name, ";

		if ($bio != '') {
			$bio = sanitize_input($bio);
		} else {
			$bio = 'NULL';
		}
		$sql .= "`bio` = $bio, ";

		if ($url != '') {
			$url = sanitize_input($url);
		} else {
			$url = 'NULL';
		}
		$sql .= "`url` = $url";

		$sql .= " WHERE `id` = $this->id";

		$query = mysqli_query($sql);

	}

	// Update a user's number of invites
	public function update_invites($invites) {

		$config = new AppConfig;

		$invites = sanitize_input($invites);

		// Get current # of invites
		$sql_get = "SELECT `invites` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}users` WHERE `id` = $this->id";
		$query = mysqli_query($sql_get);
		$old_invites = mysqli_result($query, 0);

		// Calculate new # of invites
		$new_invites = $old_invites + $invites;

		// Update database
		$sql_update = "UPDATE `{$config->database[SITE_IDENTIFIER]['prefix']}users` SET `invites` = $new_invites WHERE `id` = $this->id";
		$query = mysqli_query($sql_update);

	}

	// Check if a username is available
	public static function check_username_available($username) {

		$config = new AppConfig;

		$username = sanitize_input($username);

		$query = mysqli_query("SELECT COUNT(id) FROM `{$config->database[SITE_IDENTIFIER]['prefix']}users` WHERE `username` = $username");
		$count = mysqli_result($query, 0);

		if ($count >= 1) {

			return false;

		} else {

			return true;

		}

	}

	// Check if a given email already exists in the system
	public static function check_email_available($email) {

		$config = new AppConfig;

		$email = sanitize_input($email);

		$query = mysqli_query("SELECT COUNT(id) FROM `{$config->database[SITE_IDENTIFIER]['prefix']}users` WHERE `email` = $email");
		$user_count = mysqli_result($query, 0);

		if ($user_count >= 1) {

			return false;

		} else {

			return true;

		}

	}

	// Check if a string (usually username) contains spaces
	public static function check_contains_spaces($string) {

		$array = explode(" ", $string);

		if (count($array) > 1) {
			return true;
		} else {
			return false;
		}

	}

	// Check if a string (usually email) contains an @ symbol
	public static function check_contains_at($string) {

		$array = explode("@", $string);

		if (count($array) > 1) {
			return true;
		} else {
			return false;
		}

	}

	// Check if a string (usually username) only contains only alphanumeric characters
	public static function check_alphanumeric($string) {

		if (ctype_alnum($string)) {

			return true;

		} else {

			return false;

		}

	}

	// Check if a password reset token is valid (ie. <24hrs old), returns user_id
	public static function check_password_reset_code($code) {

		$config = new AppConfig;

		$code = sanitize_input($code);

		$sql = "SELECT `user_id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}users_password_reset` WHERE `reset_code` = $code AND `date` > DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER BY `date` DESC";
		$query = mysqli_query($sql);
		$count = mysqli_num_rows($query);

		if ($count >= 1) {

			return mysqli_result($query, 0);

		} else {

			return FALSE;

		}

	}

	// Generate a random password reset code
	public function generate_password_reset_code() {

		$config = new AppConfig;

		// Generate code
		$code = '';
		$source = 'abcdefghijklmnpqrstuvwxyz123456789';
		while (strlen($code) < 6) {
			$code .= $source[rand(0, strlen($source))];
		}

		// Write to database
		$sql = "INSERT INTO `{$config->database[SITE_IDENTIFIER]['prefix']}users_password_reset` SET `user_id` = $this->id, `reset_code` = '$code'";
		$query = mysqli_query($sql);

		return $code;

	}

}
