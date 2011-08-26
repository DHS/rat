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

	// Get all invites with a given code, returns an array of Invite objects
	public static function list_by_code($code) {
		
		$code = sanitize_input($code);
		
		$sql = "SELECT * FROM invites WHERE code = $code";
		$query = mysql_query($sql);
		
		while($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			
			$invite = new Invite;
			
			foreach ($result as $k => $v) {
				$invite->$k = $v;
			}
			
			$invites[] = $invite;
			
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