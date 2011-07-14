<?php

function user_add($email) {
	// Add a user (beta signup)
	
	$email = sanitize_input($email);

	$sql = "INSERT INTO users SET email = $email, date_added = NOW()";
	$query = mysql_query($sql);
	
	$id = mysql_insert_id();
	
	return $id;

}

function user_signup($user_id, $username, $password) {
	// Signup a new user!
	
	$user_id = sanitize_input($user_id);
	$username = sanitize_input($username);
	
	$encrypted_password = md5($password.$GLOBALS['app']['encryption_salt']);
	
	$sql = "UPDATE users SET username = $username, password = '$encrypted_password', date_joined = NOW() WHERE id = $user_id";
	$query = mysql_query($sql);

}

function user_update_password($user_id, $new_password) {
	// Change password
	
	$user_id = sanitize_input($user_id);
	
	$encrypted_password = md5($new_password.$GLOBALS['app']['encryption_salt']);
	
	$sql = "UPDATE users SET password = '{$encrypted_password}' WHERE id = $user_id";
	$query = mysql_query($sql);
	
}

function user_update_profile($user_id, $name = NULL, $bio = NULL, $url = NULL) {
	// Update profile info
	
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

function user_invites_update($user_id, $invites) {
	// Update a user's number of invites
	
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

function user_get_by_id($id) {
	// Fetch a user's info given a user_id
	
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

function user_get_by_email($email) {
	// Fetch a user's info given an email
	
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

function user_username_available($username) {
	// Check if a username is available
	
	$username = sanitize_input($username);
	
	$query = mysql_query("SELECT COUNT(id) FROM users WHERE username = $username");
	$count = mysql_result($query, 0);
	
	if ($count >= 1) {
		
		return FALSE;
		
	} else {
		
		return TRUE;
		
	}
	
}

function user_email_available($email) {
	// Check if a given email already exists in the system
	
	$email = sanitize_input($email);
	
	$query = mysql_query("SELECT COUNT(id) FROM users WHERE email = $email AND date_joined IS NOT NULL");
	$user_count = mysql_result($query, 0);
	
	if ($user_count >= 1) {
		
		return FALSE;
		
	} else {
		
		return TRUE;
		
	}
	
}

function user_contains_spaces($string) {
	// Check if a string (usually username) contains spaces
	
	$array = explode(" ", $string);
	
	if (count($array) > 1){
		return TRUE;
	} else {
		return FALSE;
	}

}

function user_contains_at($string) {
	// Check if a string (usually email) contains an @ symbol
	
	$array = explode("@", $string);
	
	if (count($array) > 1){
		return TRUE;
	} else {
		return FALSE;
	}

}

function user_alphanumeric($string) {
	// Check if a string (usually username) only contains only alphanumeric characters
	
	if (ctype_alnum($string)) {
		
		return TRUE;
		
	} else {
		
		return FALSE;
		
	}

}

?>