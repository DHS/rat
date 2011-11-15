<?php

class User {
	
	// Add a user (beta signup)	
	public static function add($email) {
		
		$email = sanitize_input($email);
		
		$sql = "INSERT INTO `users` SET `email` = $email, `date_added` = NOW()";
		$query = mysql_query($sql);
		
		$id = mysql_insert_id();
		
		return $id;
		
	}
	
	// Fetch a user's info given a user_id
	public static function get_by_id($id) {
		
		$id = sanitize_input($id);
		
		$sql = "SELECT `id`, `username`, `email`, `full_name`, `bio`, `url`, `points`, `invites`, `password`, `date_added`, `date_joined` FROM `users` WHERE `id` = $id";
		$query = mysql_query($sql);
		$result = mysql_fetch_array($query, MYSQL_ASSOC);
		
		if (!is_array($result)) {
			
			$user = NULL;
			
		} else {
			
			$user = new User;
			
			foreach ($result as $k => $v) {
				$user->$k = $v;
			}
			
		}
		
		return $user;
		
	}
	
	// Fetch a user's info given an email
	public static function get_by_username($username) {
		
		$username = sanitize_input($username);
		
		$sql = "SELECT `id`, `username`, `email`, `full_name`, `bio`, `url`, `points`, `invites`, `password`, `date_added`, `date_joined` FROM `users` WHERE `username` = $username";
		$query = mysql_query($sql);
		$result = mysql_fetch_array($query, MYSQL_ASSOC);
		
		if (!is_array($result)) {
			
			$user = NULL;
			
		} else {
			
			$user = new User;
			
			foreach ($result as $k => $v) {
				$user->$k = $v;
			}
			
		}
		
		return $user;
		
	}
	
	// Fetch a user's info given an email
	public static function get_by_email($email) {
		
		$email = sanitize_input($email);
		
		$sql = "SELECT `id`, `username`, `email`, `full_name`, `bio`, `url`, `points`, `invites`, `password`, `date_added`, `date_joined` FROM `users` WHERE `email` = $email";
		$query = mysql_query($sql);
		$result = mysql_fetch_array($query, MYSQL_ASSOC);
		
		if (!is_array($result)) {
			
			$user = NULL;
			
		} else {
			
			$user = new User;
			
			foreach ($result as $k => $v) {
				$user->$k = $v;
			}
			
		}
		
		return $user;
		
	}
	
	// Signup a new user!	
	public function signup($id, $username, $password, $salt) {
		
		$id = sanitize_input($id);
		$username = sanitize_input($username);
		
		$encrypted_password = md5($password.$salt);
		
		$sql = "UPDATE `users` SET `username` = $username, `password` = '$encrypted_password', `date_joined` = NOW() WHERE `id` = $id";
		$query = mysql_query($sql);
		
	}
	
	// Check user's login details
	public function authenticate($new_password, $salt) {
		
		if ($this->password == md5($new_password.$salt)) {
			
			// Update session
			$_SESSION['user_id'] = $this->id;
			
			// Log login
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($_SESSION['user_id'], 'user', NULL, 'login');
			}
			
			return TRUE;
			
		} else {
			
			return FALSE;
			
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
			
			return TRUE;
			
		} else {
			
			return FALSE;
			
		}
		
	}
	
	// Remove a user. WTF?
	public static function remove() {
		
		// Check item exists
		$sql_check = "SELECT `id` FROM `users` WHERE `id` = $this->id";
		$count_query = mysql_query($sql_check);
		
		if (mysql_num_rows($count_query) > 0) {
			// If user exists, go ahead and delete
			$sql_delete = "DELETE FROM `user` WHERE `id` = $this->id";
			$query = mysql_query($sql_delete);
		}
		
	}
	
	// Get a user's items, returns array of Item objects
	public function items($limit = 10, $offset = 0) {
		
		$sql = "SELECT `id` FROM `items` WHERE `user_id` = $this->id ORDER BY `id` DESC";
		
		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";
		
		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";
		
		$query = mysql_query($sql);
		
		$items = array();
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$items[] = Item::get_by_id($result['id']);
		}
		
		return $items;
		
	}
	
	// Get all invites sent by a user, returns an array of Invite objects
	public function invites($limit = 10, $offset = 0) {
		
		$sql = "SELECT `id` FROM `invites` WHERE `user_id` = $this->id ORDER BY `id` DESC";
		
		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";
		
		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";
		
		$query = mysql_query($sql);
		
		$invites = array();
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$invites[] = Invite::get_by_id($result['id']);
		}
		
		return $invites;
		
	}
	
	// Get a users's friends, returns a list of User items
	public function friends($limit = 10, $offset = 0) {
		
		$sql = "SELECT `id`, `friend_user_id` FROM `friends` WHERE `user_id` = $this->id";
		
		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";
		
		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";
		
		$query = mysql_query($sql);
		
		$friends = array();
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$friends[$result['id']] = User::get_by_id($result['friend_user_id']);
		}
		
		return $friends;
		
	}
	
	// Get a users's followers, returns a list of Friend items
	public function followers($limit = 10, $offset = 0) {
		
		$sql = "SELECT `id`, `friend_user_id` FROM `friends` WHERE `friend_user_id` = $this->id";
		
		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";
		
		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";
		
		$query = mysql_query($sql);
		
		$friends = array();
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$friends[$result['id']] = User::get_by_id($result['user_id']);
		}
		
		return $friends;
		
	}
	
	// Get items liked by a user, returns array of Item objects
	public function likes($limit = 10, $offset = 0) {
			
		$sql = "SELECT `item_id` FROM `likes` WHERE `user_id` = $this->id ORDER BY `date` DESC";
		
		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";
		
		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";
		
		$query = mysql_query($sql);
		
		$items = array();
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$items[] = Item::get_by_id($result['item_id']);
		}
		
		return $items;
		
	}
	
	// Get comments made by a user, returns an array of Comment objects
	public function comments($limit = 10, $offset = 0) {
		
		$sql = "SELECT `id` FROM `comments` WHERE `user_id` = $this->id ORDER BY `id` ASC";
		
		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";
		
		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";
		
		$query = mysql_query($sql);
		
		$comments = array();
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$comments[] = Comment::get_by_id($result['id']);
		}
		
		return $comments;
		
	}
	
	// Add a friend, returns friendship id
	public function friend_add($friend_user_id) {
		
		$friend_user_id = sanitize_input($friend_user_id);
		
		$count_sql = "SELECT `id` FROM `friends` WHERE `user_id` = {$this->id} AND `friend_user_id` = $friend_user_id";
		$count_query = mysql_query($count_sql);
		
		if (mysql_num_rows($count_query) < 1) {
			$sql = "INSERT INTO `friends` SET `user_id` = {$this->id}, `friend_user_id` = $friend_user_id";
			$query = mysql_query($sql);
		}
		
	}
	
	// Update a friendship status
	public function friend_update($friend_user_id, $status) {
		
		$friend_user_id = sanitize_input($friend_user_id);
		$status = sanitize_input($status);
		
		$sql = "UPDATE `friends` SET `status` = $status WHERE `user_id` = {$this->id} AND `friend_user_id` = {$friend_user_id}";
		$query = mysql_query($sql);
		
	}
	
	// Unfriend! Returns friendship id
	public function friend_remove($friend_user_id) {
		
		$friend_user_id = sanitize_input($friend_user_id);
		
		$count_sql = "SELECT `id` FROM `friends` WHERE `user_id` = {$this->id} AND `friend_user_id` = {$friend_user_id}";
		$count_query = mysql_query($count_sql);
		
		if (mysql_num_rows($count_query) > 0) {
			$sql = "DELETE FROM `friends` WHERE `user_id` = {$this->id} AND `friend_user_id` = {$friend_user_id}";
			$query = mysql_query($sql);
		}
		
	}
	
	// Get a feed of a friend's activity, returns array of Item objects
	public function list_feed($limit = 10, $offset = 0) {
		
		// Start by adding the viewer to the query string
		$friends_string = "`user_id` = {$this->id}";
		
		$friends = $this->friends();
		
		// Loop through friends adding them to the query string
		foreach ($friends as $friend) {
			$friends_string .= " OR `user_id` = {$friend['friend_user_id']}";
		}
		
		$sql = "SELECT `id` FROM `items` WHERE $friends_string ORDER BY `id` DESC";
		
		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";
		
		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";
		
		$query = mysql_query($sql);
		
		// Loop through item ids, fetching objects
		$items = array();
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$items[] = Item::get_by_id($result['id']);
		}
		
		return $items;
		
	}
	
	// Check whether two users are friends, returns TRUE or FALSE
	public function friend_check($friend_user_id) {
		
		$friend_user_id = sanitize_input($friend_user_id);
		
		$sql = "SELECT COUNT(id) FROM `friends` WHERE `user_id` = {$this->id} AND `friend_user_id` = $friend_user_id";
		$query = mysql_query($sql);
		$result = mysql_result($query, 0);
		
		if ($result > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
		
	}
	
	// Change password
	public function update_password($new_password, $salt) {
		
		$encrypted_password = md5($new_password.$salt);
		
		$sql = "UPDATE `users` SET `password` = '{$encrypted_password}' WHERE `id` = $this->id";
		$query = mysql_query($sql);
		
	}
	
	// Update profile info
	public function update_profile($name = NULL, $bio = NULL, $url = NULL) {
		
		$sql = "UPDATE `users` SET ";
		
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
		
		$query = mysql_query($sql);
		
	}
	
	// Update a user's number of invites
	public function update_invites($invites) {
		
		$invites = sanitize_input($invites);
		
		// Get current # of invites
		$sql_get = "SELECT `invites` FROM `users` WHERE `id` = $this->id";
		$query = mysql_query($sql_get);
		$old_invites = mysql_result($query, 0);
		
		// Calculate new # of invites
		$new_invites = $old_invites + $invites;
		
		// Update database
		$sql_update = "UPDATE `users` SET `invites` = $new_invites WHERE `id` = $this->id";
		$query = mysql_query($sql_update);
		
	}
	
	// Check if a username is available
	public static function check_username_available($username) {
		
		$username = sanitize_input($username);
		
		$query = mysql_query("SELECT COUNT(id) FROM `users` WHERE `username` = $username");
		$count = mysql_result($query, 0);
		
		if ($count >= 1) {
			
			return FALSE;
			
		} else {
			
			return TRUE;
			
		}
		
	}
	
	// Check if a given email already exists in the system
	public static function check_email_available($email) {
		
		$email = sanitize_input($email);
		
		$query = mysql_query("SELECT COUNT(id) FROM `users` WHERE `email` = $email");
		$user_count = mysql_result($query, 0);
		
		if ($user_count >= 1) {
			
			return FALSE;
			
		} else {
			
			return TRUE;
			
		}
		
	}
	
	// Check if a string (usually username) contains spaces
	public static function check_contains_spaces($string) {
		
		$array = explode(" ", $string);
		
		if (count($array) > 1) {
			return TRUE;
		} else {
			return FALSE;
		}
		
	}

	// Check if a string (usually email) contains an @ symbol
	public static function check_contains_at($string) {
		
		$array = explode("@", $string);
		
		if (count($array) > 1) {
			return TRUE;
		} else {
			return FALSE;
		}
		
	}

	// Check if a string (usually username) only contains only alphanumeric characters
	public static function check_alphanumeric($string) {
		
		if (ctype_alnum($string)) {
			
			return TRUE;
			
		} else {
			
			return FALSE;
			
		}
		
	}
	
	// Check if a password reset token is valid (ie. <24hrs old), returns user_id
	public static function check_password_reset_code($code) {
		
		$code = sanitize_input($code);
        
		$sql = "SELECT `user_id` FROM `users_password_reset` WHERE `reset_code` = $code AND `date` > DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER BY `date` DESC";
		$query = mysql_query($sql);
		$count = mysql_num_rows($query);
		
		if ($count >= 1) {
        	
			return mysql_result($query, 0);
        	
		} else {
        	
			return FALSE;
        	
		}
		
	}
	
	// Generate a random password reset code
	public function generate_password_reset_code() {
		
		// Generate code
		$code = '';
		$source = 'abcdefghijklmnpqrstuvwxyz123456789';
		while (strlen($code) < 6) {
			$code .= $source[rand(0, strlen($source))];
		}
		
		// Write to database
		$sql = "INSERT INTO `users_password_reset` SET `user_id` = $this->id, `reset_code` = '$code'";
		$query = mysql_query($sql);

		return $code;
		
	}
	
}

?>
