<?php

function invites_add($user_id, $email) {
	// Add an invite
	
	$user_id = sanitize_input($user_id);
	$email = sanitize_input($email);

	$insert_sql = "INSERT INTO invites SET user_id = $user_id, email = $email";
	$insert_query = mysql_query($insert_sql);

	$id = mysql_insert_id();
	
	$update_sql = "UPDATE invites SET code = '$id' WHERE id = $id";
	$query = mysql_query($update_sql);
	
	return $id;
	
}

function invites_sent($user_id) {
	// Get all invites sent by a user
	
	$user_id = sanitize_input($user_id);
	
	$sql = "SELECT id, email, result FROM invites WHERE user_id = $user_id ORDER BY id DESC";
	$query = mysql_query($sql);
	
	while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
		$invites_sent[] = $result;
	}

	return $invites_sent;
	
}

function invites_remaining($user_id) {
	// Get number of remaining invites available to a user

	$invites_get_remaining = 5;
	
	return $invites_get_remaining;

}

function invites_update($id) {
	// Update an invite

	$id = sanitize_input($id);
	
	$sql_get = "SELECT result FROM invites WHERE id = $id";
	$query_get = mysql_query($sql_get);
	$old_result = mysql_result($query_get, 0);
	
	$new_result = $old_result + 1;
	
	// Update database
	$sql_update = "UPDATE invites SET result = $new_result WHERE id = $id";
	$query_update = mysql_query($sql_update);

}

function invites_already_invited($user_id, $email) {
	// Checks to see if a user is already invited
	
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

function invites_get_by_code($code) {
	
	$code = sanitize_input($code);

	$invites = NULL;
	
	$sql_count = "SELECT COUNT(id) FROM invites WHERE code = $code";
	$count_query = mysql_query($sql_count);
	$count = mysql_result($count_query, 0);
	
	if ($count >= 1) {
		
		$sql_get = "SELECT * FROM invites WHERE code = $code";
		$query = mysql_query($sql_get);
		
		while ($invite = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$invites[] = $invite;
		}
		
	}
	
	return $invites;
	
}

function validate_invite_code($code, $email) {
	// Validates an invite code
	// Doesn't need to check if blank as that's taken care of by the selector in signup.php
	
	if ($code == '')
		return FALSE;
	
	$code = sanitize_input($code);
	$email = sanitize_input($email);
	
	$sql = "SELECT result FROM invites WHERE code = $code AND email = $email";
	echo $sql;
	$query = mysql_query($sql);
	$status = mysql_result($query, 0);
	
	if ($status === 0) {
		return TRUE;
	} else {
		return FALSE;
	}

}

?>