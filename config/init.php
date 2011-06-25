<?php


// Setup database variables

$database['dev'] = array(	'host'		=> 'localhost',
							'username'	=> 'root',
							'password'	=> 'root',
							'database'	=> 'rat'
						);

$database['live'] = array(	'host'		=> 'localhost',
							'username'	=> 'username',
							'password'	=> 'password',
							'database'	=> 'database'
						);


// Determine whether site is dev or live

//$domain = substr(substr($app['url'], 0, -1), 7);
//
//switch ($_SERVER['HTTP_HOST']) {
//
//	case $domain:
//		define('SITE_IDENTIFIER', 'live');
//		break;
//
//	case 'www.'.$domain:
//		define('SITE_IDENTIFIER', 'live');
//		break;
//
//	case $domain:
//		define('SITE_IDENTIFIER', 'dev');
//		break;
//
//}

define('SITE_IDENTIFIER', 'dev');

// Establish database connection

$connection = mysql_pconnect($database[SITE_IDENTIFIER]['host'], $database[SITE_IDENTIFIER]['username'], $database[SITE_IDENTIFIER]['password'])
	or die ("Couldn't connect to server.");
$db = mysql_select_db($database[SITE_IDENTIFIER]['database'], $connection)
	or die("Couldn't select database.");


// MySQL injection protection function

function sanitize_input($input) {
	
	if ($input === TRUE) {
		return "'TRUE'";
	} elseif ($input === FALSE) {
		return "'FALSE'";
	}
	
	if (get_magic_quotes_gpc())
		$input = stripslashes($input);

	// If not a number, then add quotes
	if (!is_numeric($input))
		$input = "'".mysql_real_escape_string($input)."'";
		
	return $input;
	
}


// Load app variables

$sql = "SELECT * FROM options ORDER BY option_id ASC";
$query = mysql_query($sql);

while ($row = mysql_fetch_array($query, MYSQL_ASSOC))
	$app[$row['option_name']] = unserialize($row['option_value']);

//// Find available plugins
//
//$handle = opendir('plugins');
//while (false !== ($file = readdir($handle))) {
//	if ($file[0] != '.') {
//		$plugins[] = substr($file, 0, -4);
//	}
//}


// Load plugins

foreach ($app['plugins'] as $plugin) {
	include_once "plugins/$plugin.php";
	$$plugin = new $plugin;
}


// Start session
session_start();


// Load admin model
require_once 'models/admin.php';


// Check if app is private, if so and page is not public then STOP!
if ($app['private'] == TRUE && count(admin_get_users()) != 0) {
	// App requires login to view pages

	// Finds page name
	preg_match("/[a-zA-Z0-9]+\.php/", $_SERVER['PHP_SELF'], $result);
	
	if (empty($_SESSION['user']) && in_array($result[0], $app['public_pages']) != TRUE) {
		// User not logged in and page is not in $app['public_pages'] so show holding page

		include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
		include 'themes/'.$GLOBALS['app']['theme'].'/splash.php';
		include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';
	
		// Stop processing the rest of the page
		exit();
	
	}

}


// Load models
require_once 'models/user.php';
require_once 'models/invites.php';
require_once 'models/friends.php';
require_once 'models/items.php';
require_once 'models/comments.php';
require_once 'models/likes.php';

?>