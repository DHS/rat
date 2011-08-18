<?php

class config {

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
	public $name							= 'Ratter';
	public $tagline							= 'Demo of rat\'s functionality';

	// URLs - must include http:// and a trailing slash
	public $url								= 'http://example.com/';
	public $dev_url							= 'http://localhost:8888/';

	// Base directory - the directory in which your site resides if not in the server root
	public $base_dir          = '/';
	public $dev_base_dir      = '';

	// Default route - the controller to use if not specified in the URL
	public $default_controller = 'items';

	// Beta - users can't signup, can only enter their email addresses
	public $beta							= TRUE;

	// Email enabled - search project for "// Email user" to find what this affects
	public $send_emails						= FALSE;

	// Private app - requires login to view pages (except public_pages), no share buttons
	public $private							= TRUE;
	public $public_pages					= array('signup.php', 'login.php', 'logout.php', 'help.php');

	// Items
	// Notes about uploads: max-size is in bytes (default: 5MB), directory should contain three subdirectories: originals, thumbnails, stream
	public $items = array(	'name'			=> 'post',
							'name_plural'	=> 'posts',
							'titles'		=> array('enabled' => TRUE, 'name' => 'Title', 'name_plural' => 'Titles'),
							'content'		=> array('enabled' => TRUE, 'name' => 'Content', 'name_plural' => 'Contents'),
							'uploads'		=> array('enabled' => FALSE, 'name' => 'Image', 'directory' => 'uploads', 'max-size' => '5242880', 'mime-types' => array('image/jpeg', 'image/png', 'image/gif', 'image/pjpeg')),
							'comments'		=> array('enabled' => TRUE, 'name' => 'Comment', 'name_plural' => 'Comments'),
							'likes'			=> array('enabled' => TRUE, 'name' => 'Like', 'name_plural' => 'Likes', 'opposite_name' => 'Unlike', 'past_tense' => 'Liked by')
							);

	// Invites system
	public $invites = array('enabled' => TRUE);

	// Friends - still testing, works with asymmetric set to true... just! (Shows 'Follow' link & generates homepage feed)
	public $friends = array('enabled' => FALSE, 'asymmetric' => FALSE);

	// Admin users - array of user IDs who have access to admin area
	public $admin_users						= array(1);

	// Encryption salt - change to a random six character string, do not change after first use of application
	public $encryption_salt					= 'hw9e46';

	// THEMES

	public $theme = 'default';

	// DATABASE
	
	public $database = array(	'dev'	=> array(	'host'		=> 'localhost',
													'username'	=> 'root',
													'password'	=> 'root',
													'database'	=> 'rat'
												),
												
								'live'	=> array(	'host'		=> 'localhost',
													'username'	=> 'root',
													'password'	=> '',
													'database'	=> 'rat'
												)
							);
	
	// PLUGINS
    
	public $plugins = array(	'log'		=> TRUE,
								'gravatar'	=> TRUE,
								'points'	=> FALSE,
								'analytics'	=> FALSE
							);

	
}

?>