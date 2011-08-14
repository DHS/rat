<?php

/*
*	A log plugin for Rat by @DHS
*
*	Installation
*	
*		Comes installed by default
*
*	Usage
*	
*		To log an event:
*		
*			if (isset($app->plugins->log))
*				$app->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'signup');
*
*/

class log {

	function add($user_id, $object_type = NULL, $object_id = NULL, $action, $params = NULL) {
		// Add a new entry to the log
		
		$user_id = sanitize_input($user_id);	
		$object_type = sanitize_input($object_type);
		$object_id = sanitize_input($object_id);
		$action = sanitize_input($action);
		$params = sanitize_input($params);
		
		$query = mysql_query("INSERT INTO log SET user_id = $user_id, object_type = $object_type, object_id = $object_id, action = $action, params = $params");
		
	}
	
	function view() {
		// View the log
		
		global $app;
		
		$sql = "SELECT * FROM log ORDER BY id DESC LIMIT 10";
		$query = mysql_query($sql);

		while ($entry = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$entry['user'] = $app->user->get($entry['user_id']);
			$entries[] = $entry;
		}
		
		if (is_array($entries)) {
			
			echo '<table>';
			echo '<tr><th>Action</th><th>Object</th><th>Params</th><th>Timestamp</th></tr>';
			foreach ($entries as $entry) {
				echo '<tr><td><a href="/'.$entry['user']['username'].'">'.$entry['user']['name'].'</a> '.$entry['object_type'].' '.$entry['action'].'</td><td>';
				if ($entry['object_id'] != NULL)
					echo $entry['object_id'];
				echo '</td><td>';
				if ($entry['params'] != NULL)
					echo $entry['params'];
				echo '</td><td>'.$entry['date'].'</td></tr>';
			}
			echo '</table>';
		
		}
	
	}

}

?>