<?php

/*
*	Contents
*
*		1. Basic app variables
*		2. URLs
*		3. Beta
*		4. Privacy
*		5. Items
*		6. Invites
*		7. Friends
*		8. Admin
*		9. Encryption
*		10. Database
*		11. Themes
*		12. Plugins
*		
*/

// Basic app variables
$app['name']							= 'Ratter';
$app['tagline']							= 'Demo of rat\'s functionality';

// URLs - must include http:// and trailing slash
$app['url']								= 'http://example.com/';
$app['dev_url']							= 'http://localhost/';

// Beta - users can't signup, can only enter their email addresses
$app['beta']							= TRUE;

// Email enabled - search project for "// Email user" to find what this affects
$app['send_emails']						= FALSE;

// Private app - requires login to view pages (except public_pages), no share buttons
$app['private']							= TRUE;
$app['public_pages']					= array('signup.php', 'login.php', 'logout.php', 'help.php');

// Items
$app['items'] = array(	'name'			=> 'post',
						'name_plural'	=> 'posts',
						'titles'		=> array('enabled' => TRUE, 'name' => 'Title'),
						'content'		=> array('enabled' => TRUE, 'name' => 'Content'),
						'comments'		=> array('enabled' => TRUE, 'name' => 'Comment', 'name_plural' => 'Comments'),
						'likes'			=> array('enabled' => TRUE, 'name' => 'Like', 'opposite_name' => 'Unlike', 'past_tense' => 'Liked by')
						);

// Invites system
$app['invites']['enabled']				= TRUE;

// Friends - still testing, works with asymmetric set to true... just! (Shows 'Follow' link & generates homepage feed)
$app['friends']['enabled']				= FALSE;
$app['friends']['asymmetric']			= FALSE;

// Admin users - array of user IDs who have access to admin area
$app['admin_users']						= array(1);

// Encryption salt - change to a random six character string, do not change after first use of application
$app['encryption_salt']					= 'hw9e46';


// DATABASE

// Determine whether site is dev or live
$domain = substr(substr($app['url'], 0, -1), 7);

switch ($_SERVER['HTTP_HOST']) {

	case $domain:
		define('SITE_IDENTIFIER', 'live');
		break;

	case 'www.'.$domain:
		define('SITE_IDENTIFIER', 'live');
		break;

	case $domain:
		define('SITE_IDENTIFIER', 'dev');
		break;

}

if (SITE_IDENTIFIER == 'live') {
	// Live database vars

	$app['database'] = array(	'host'		=> 'localhost',
								'username'	=> 'username',
								'password'	=> 'password',
								'database'	=> 'database'
							);

} else {
	// Dev database vars

	$app['database'] = array(	'host'		=> 'localhost',
								'username'	=> 'root',
								'password'	=> 'root',
								'database'	=> 'rat'
							);

}


// THEMES

$app['theme'] = 'default';


// PLUGINS

// Log plugin

include_once 'plugins/log.php';
$log = new log;

// Gravatar plugin

include_once 'plugins/gravatar.php';
$gravatar = new gravatar;

// Points system plugin

//include_once 'plugins/points.php';
//$points = new points;
//
//$app['points']['name']					= 'points';
//$app['points']['per_item']				= 1;
//$app['points']['per_invite_sent']		= 1;
//$app['points']['per_invite_accepted']	= 10;
//$app['points']['leaderboard']			= FALSE;

// Google analytics plugin

//include_once 'plugins/analytics.php';
//$analytics = new analytics('123');

?>