<?php

class Invite {
	
	// Add an invite, returns invite id
	public static function add($user_id, $email) {
		
		$user_id = sanitize_input($user_id);
		$email = sanitize_input($email);
		
		$insert_sql = "INSERT INTO invites SET user_id = $user_id, email = $email";
		$insert_query = mysql_query($insert_sql);
		
		$id = mysql_insert_id();
		
		$update_sql = "UPDATE invites SET code = '$id' WHERE id = $id";
		$query = mysql_query($update_sql);
		
		return $id;
		
	}
	
	// Get a single invite, returns an Invite object
	public static function get_by_id($id) {
		
		$id = sanitize_input($id);
        
		$sql = "SELECT id, user_id, email, code, result, date FROM invites WHERE id = $id";
		$query = mysql_query($sql);
		$result = mysql_fetch_array($query, MYSQL_ASSOC);
		
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
		
		$code = sanitize_input($code);
		
		$sql = "SELECT id FROM invites WHERE code = $code";
		
		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";
		
		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";
		
		$query = mysql_query($sql);
		
		// Loop through invite ids, fetching objects
		$invites = array();
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$invites[] = Invite::get_by_id($result['id']);
		}
		
		return $invites;
		
	}
	
	// Update an invite	
	public function update() {
		
		$sql_get = "SELECT result FROM invites WHERE id = $this->id";
		$query_get = mysql_query($sql_get);
		$old_result = mysql_result($query_get, 0);
		
		$new_result = $old_result + 1;
		
		// Update database
		$sql_update = "UPDATE invites SET result = $new_result WHERE id = $this->id";
		$query_update = mysql_query($sql_update);
		
	}
	
	// Checks to see if a user is already invited, returns TRUE or FALSE
	public static function check_invited($user_id, $email) {
		
		$user_id = sanitize_input($user_id);
		$email = sanitize_input($email);
		
		$sql = "SELECT COUNT(id) FROM invites WHERE user_id = $user_id AND email = $email";
		$query = mysql_query($sql);
		$user_count = mysql_result($query, 0);
		
		if ($user_count >= 1) {
			
			return TRUE;
			
		} else {
			
			return FALSE;
			
		}
		
	}
	
	// Validates an invite code, returns TRUE or FALSE
	public static function check_code_valid($code, $email) {
		
		if ($code == '') {
			return FALSE;
		}
		
		$code = sanitize_input($code);
		$email = sanitize_input($email);
		
		$sql = "SELECT result FROM invites WHERE code = $code AND email = $email";
		$query = mysql_query($sql);
		$status = mysql_num_rows($query);
		
		if ($status > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
		
	}
	
}

?>