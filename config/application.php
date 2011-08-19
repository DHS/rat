<?php

class AppConfig extends ServerConfig {

	/*
	*	Contents
	*
	*		1. Basic app variables
	*		2. Beta
	*		3. Privacy
	*		4. Items
	*		5. Invites
	*		6. Friends
	*		7. Admin
	*		8. Themes
	*		9. Plugins
	*		
	*/

	// Basic app variables
	public $name							= 'Ratter';
	public $tagline							= 'Demo of rat\'s functionality';
	public $default_controller				= 'items';

	// Beta - users can't signup, can only enter their email addresses
	public $beta							= TRUE;

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

	// Theme
	public $theme = 'default';

	// Plugins
	public $plugins = array(	'log'		=> TRUE,
								'gravatar'	=> TRUE,
								'points'	=> FALSE,
								'analytics'	=> FALSE
							);
	
}

?>