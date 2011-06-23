<?php

function admin_get_users() {
	// Get all users

	$sql = "SELECT * FROM users WHERE date_joined IS NOT NULL ORDER BY date_joined DESC";
	$users_query = mysql_query($sql);
	while ($user = mysql_fetch_array($users_query, MYSQL_ASSOC)) {
		
		// Find last login
		$last_login_query = mysql_query("SELECT TIMESTAMPDIFF(DAY, date, NOW()) FROM log WHERE user_id = '{$user['id']}' AND action = 'login' ORDER BY date DESC LIMIT 1");
		if (mysql_num_rows($last_login_query) > 0) {
			$last_login = mysql_result($last_login_query, 0);
			if ($last_login == 0) {
				$last_login = 'Today!';
			} else {
				$last_login = $last_login.' days ago';
			}
		}  else {
			$last_login = 'Never';
		}
		
		$user['last_login'] = $last_login;
		
		$users[] = $user;
		
	}

	return $users;
	
}

function admin_get_waiting_users() {
	// Get beta signups who are still waiting for an invite

	$sql = "SELECT id, email, TIMESTAMPDIFF(DAY, date_added, NOW()) AS days_waiting, (SELECT COUNT(*) FROM invites WHERE email = users.email) AS invites FROM users WHERE date_joined IS NULL ORDER BY date_added ASC";
	$waiting_users_query = mysql_query($sql);
	
	while ($user = mysql_fetch_array($waiting_users_query, MYSQL_ASSOC)) {
		$waiting_users[] = $user;
	}
	
	return $waiting_users;
	
}

function admin_grant_invites($invites) {
	// Grants a given number of invites to all users
	
	$invites = sanitize_input($invites);
	
	$users = admin_get_users();
	
	foreach ($users as $user) {
		
		$new_invites = $user['invites'] + $invites;
		
		// uncomment the following line to zero invites
		//$user['invites'] = 0;
		
		$query = mysql_query("UPDATE users SET invites = $new_invites WHERE id = {$user['id']}");
		
	}
	
	// update own invites
	$_SESSION['user']['invites'] = $_SESSION['user']['invites'] + $_GET['count'];
	
}

function admin_update_item($id, $title = NULL, $byline = NULL, $content = NULL, $status = 1) {
	// Updates an item
	
	$id = sanitize_input($id);
	
	$update_string = '';
	
	if ($title != NULL) {
		$title = sanitize_input($title);
		$update_string .= "title = $title, ";
	}
	
	if ($content != NULL) {
		$content = sanitize_input($content);
		$update_string .= "content = $content, ";
	}
	
	$status = sanitize_input($status);
	$update_string .= "status = $status";

	$query = mysql_query("UPDATE items SET $update_string WHERE id = $id");
	
}


?>