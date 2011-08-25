<?php

class User {

	// Add a user (beta signup)	
	public static function add($email) {

		$email = sanitize_input($email);

		$sql = "INSERT INTO users SET email = $email, date_added = NOW()";
		$query = mysql_query($sql);

		$id = mysql_insert_id();

		return $id;

	}

	// Fetch a user's info given a user_id
	public static function get_by_id($id) {

		$id = sanitize_input($id);

		$sql = "SELECT * FROM users WHERE id = $id";
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

		$sql = "SELECT * FROM users WHERE username = $username";
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

		$sql = "SELECT * FROM users WHERE email = $email";
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
	public static function signup($user_id, $username, $password) {
		
		$user_id = sanitize_input($user_id);
		$username = sanitize_input($username);

		$encrypted_password = md5($password.$this->config->encryption_salt);

		$sql = "UPDATE users SET username = $username, password = '$encrypted_password', date_joined = NOW() WHERE id = $user_id";
		$query = mysql_query($sql);

	}
	
	// Get a user's items, returns array of Item objects
	public function items($user_id, $limit = 10, $offset = 0) {
			
		$user_id = sanitize_input($user_id);

		$sql = "SELECT * FROM items WHERE user_id = $user_id ORDER BY id DESC";

		// Limit not null so create limit string
		if ($limit != NULL) {
			$limit = sanitize_input($limit);
			$sql .= " LIMIT $limit";
		}

		// Offset not zero so create offset string
		if ($offset != NULL) {
			$offset = sanitize_input($offset);
			$sql .= " OFFSET $offset";
		}

		$query = mysql_query($sql);

		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {

			$item = new Item;

			foreach($result as $k => $v) {
				$item->$k = $v;
			}

			$item->user = User::get_by_id($result['user_id']);
			$item->comments = Item::comments($result['id']);
			$item->likes = Item::likes($result['id']);

			$items[] = $item;

		}

		return $items;

	}
	
	// Get all invites sent by a user, returns an array of Invite objects
	public function invites($user_id) {

		$user_id = sanitize_input($user_id);

		$sql = "SELECT id, email, result FROM invites WHERE user_id = $user_id ORDER BY id DESC";
		$query = mysql_query($sql);

		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			
			$invite = new Invite;
			
			foreach($result as $k => $v) {
				$invite->$k = $v;
			}
			
			$invites[] = $invite;
			
		}

		return $invites;

	}
	
	// Get a users's friends, returns a list of Friend items
	public function friends($user_id) {

		$user_id = sanitize_input($user_id);

		$sql = "SELECT id, user_id, friend_user_id, status, date_added, date_updated FROM friends WHERE user_id = $user_id";
		$query = mysql_query($sql);

		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			
			$friend = new Friend;
			
			foreach($result as $k => $v) {
				$friend->$k = $v;
			}
			
			$friend->user = User::get_by_id($result['friend_user_id']);
			
			$friends[$result['id']] = $friend;
			
		}

		return $friends;

	}
	
	// Get a users's followers, returns a list of Friend items
	public function followers($user_id) {

		$user_id = sanitize_input($user_id);

		$return = NULL;

		$sql = "SELECT id, user_id, friend_user_id, status, date_added, date_updated FROM friends WHERE friend_user_id = $user_id";
		$query = mysql_query($sql);
		
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			
			$friend = new Friend;
			
			foreach($result as $k => $v) {
				$friend->$k = $v;
			}
			
			$friend->user = User::get_by_id($result['user_id']);
			
			$friends[$result['id']] = $friend;
			
		}
		
		return $friends;

	}
	
	// Get items liked by a user, returns array of Item objects
	public function likes($user_id, $limit = 10) {
		
		$user_id = sanitize_input($user_id);
		
		$sql = "SELECT item_id FROM likes WHERE user_id = $user_id AND status = 1 ORDER BY date DESC";
		
		// Limit not null so create limit string
		if ($limit != NULL) {
			$limit = sanitize_input($limit);
			$sql .= " LIMIT $limit";
		}
		
		$query = mysql_query($sql);
		
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			
			// Instantiate new object? Or will this suffice:
			
			$item = Item::get_by_id($result['item_id']);
			$item->user = User::get_by_id($item->user_id);
			$item->comments = Item::comments($result['item_id']);
			$item->likes = Item::likes($result['item_id']);
			
			$items[] = $item;
			
		}
		
		return $items;
		
	}
	
	// Get comments made by a user, returns an array of Comment objects
	public function comments($user_id) {
		
		$item_id = sanitize_input($item_id);
		
		$sql = "SELECT id, content, user_id, date FROM comments WHERE user_id = $user_id ORDER BY id ASC";
		$query = mysql_query($sql);
		
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			
			$comment = Comment::get_by_id($result['id']);
			
			$comments[] = $comment;
			
		}
		
		return $comments;
		
	}
	
	// Change password
	public static function update_password($user_id, $new_password) {

		$user_id = sanitize_input($user_id);

		$encrypted_password = md5($new_password.$this->config->encryption_salt);

		$sql = "UPDATE users SET password = '{$encrypted_password}' WHERE id = $user_id";
		$query = mysql_query($sql);

	}

	// Update profile info
	public static function update_profile($user_id, $name = NULL, $bio = NULL, $url = NULL) {

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
	public static function update_invites($user_id, $invites) {

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
	public static function check_username_available($username) {

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
	public static function check_email_available($email) {

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
	
	// Check if a password reset token is valid ie. <24hrs old
	public static function check_password_reset_code($code) {
		
		$code = sanitize_input($code);
        
		$sql = "SELECT user_id FROM users_password_reset WHERE reset_code = $code AND date > DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER BY date DESC";
		$query = mysql_query($sql);
		$count = mysql_num_rows($query);
		
		if ($count >= 1) {
        
			return mysql_result($query, 0);
        
		} else {
        
			return FALSE;
        
		}
		
	}
	
	// Generate a random password reset code
	public static function generate_password_reset_code($user_id) {
		
		// Generate code
		$code = '';
		$source = 'abcdefghijklmnpqrstuvwxyz123456789';
		while (strlen($code) < 6) {
			$code .= $source[rand(0, strlen($source))];
		}
		
		// Write to database
		$sql = "INSERT INTO users_password_reset SET user_id = $user_id, reset_code = '$code'";
		$query = mysql_query($sql);

		return $code;
		
	}
	
}

?>
