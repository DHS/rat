<?php

// Create database connection

$connection = mysql_pconnect($app->config->database[SITE_IDENTIFIER]['host'], $app->config->database[SITE_IDENTIFIER]['username'], $app->config->database[SITE_IDENTIFIER]['password'])
	or die ("Couldn't connect to server.");
$db = mysql_select_db($app->config->database[SITE_IDENTIFIER]['database'], $connection)
	or die("Couldn't select database.");


// SQL injection protection function

function sanitize_input($input) {
	
	if (get_magic_quotes_gpc())
		$input = stripslashes($input);
    
	// If not a number, then add quotes
	if (!is_numeric($input))
		$input = "'".mysql_real_escape_string($input)."'";
	
	return $input;

}

?>