<?php

class Admin {
	
	// Get all users	
	public static function list_users() {
		$config = new AppConfig;
		
		$sql = "SELECT * FROM `{$config->database[SITE_IDENTIFIER]['prefix']}users` WHERE `date_joined` IS NOT NULL ORDER BY `date_joined` DESC";
		$users_query = mysql_query($sql);
		
		$users = array();
		while ($user = mysql_fetch_array($users_query, MYSQL_ASSOC)) {
			
			// Find last login
			$last_login_query = mysql_query("SELECT TIMESTAMPDIFF(DAY, date, NOW()) FROM `log` WHERE `user_id` = '{$user['id']}' AND `action` = 'login' ORDER BY `date` DESC LIMIT 1");
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
	
	// Get beta signups who are still waiting for an invite
	public static function list_users_beta() {
		$config = new AppConfig;

		$sql = "SELECT `id`, `email`, TIMESTAMPDIFF(DAY, date_added, NOW()) AS days_waiting, (SELECT COUNT(*) FROM `invites` WHERE `email` = {$config->database[SITE_IDENTIFIER]['prefix']}users.email) AS invites FROM `{$config->database['SITE_IDENTIFIER']['prefix']}users` WHERE `date_joined` IS NULL ORDER BY `date_added` ASC";
		$waiting_users_query = mysql_query($sql);
		
		$waiting_users = array();
		while ($user = mysql_fetch_array($waiting_users_query, MYSQL_ASSOC)) {
			$waiting_users[] = $user;
		}
		
		return $waiting_users;
		
	}
	
	// Grants a given number of invites to all users
	public static function update_invites($invites) {
		$config = new AppConfig;

		$invites = sanitize_input($invites);
		
		$users = Admin::list_users();
		
		foreach ($users as $user) {
			
			$new_invites = $user['invites'] + $invites;
			
			// uncomment the following line to zero invites
			//$user['invites'] = 0;
			
			$query = mysql_query("UPDATE `{$config->database[SITE_IDENTIFIER]['prefix']}users` SET `invites` = $new_invites WHERE id = {$user['id']}");
			
		}
		
	}
	
	// Updates an item
	public static function update_item($id, $title = NULL, $byline = NULL, $content = NULL, $status = 1) {
		$config = new AppConfig;

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
		
		$query = mysql_query("UPDATE `{$config->database[SITE_IDENTIFIER]['prefix']}items` SET $update_string WHERE id = $id");
		
	}
	
	public static function tables_exist() {
			
		$config = new AppConfig;
	
		return mysql_query("SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}items") !== FALSE;
			
	}
	
}

?>
