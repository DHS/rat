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
*			if (is_object($GLOBALS['log']))
*				$GLOBALS['log']->add($_SESSION['user']['id'], 'user', NULL, 'signup');
*
*/

class log {

	function add($user_id, $object_type = NULL, $object_id = NULL, $action, $params = NULL) {
	
		$user_id = sanitize_input($user_id);	
		$object_type = sanitize_input($object_type);
		$object_id = sanitize_input($object_id);
		$action = sanitize_input($action);
		$params = sanitize_input($params);
		
		$query = mysql_query("INSERT INTO log SET user_id = $user_id, object_type = $object_type, object_id = $object_id, action = $action, params = $params");
		
	}

}

?>