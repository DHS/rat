<?php

class ServerConfig {

	// URLs - must include http:// and no trailing slash
	public $url     = 'http://example.com';
	public $dev_url = 'http://localhost';

	// Base directory - the directory in which your site resides if not in the server root
	public $base_dir      = '/';
	public $dev_base_dir  = '/';

	// Email enabled - search project for "// Email user" to find what this affects
	public $send_emails = FALSE;

	// Encryption salt - change to a random six character string, do not change after first use of application
	public $encryption_salt = 'hw9e46';

	// Set timezone
	public $timezone = 'Europe/London';

	// Database
	public $database = array(
	  'dev'	=> array(
	    'host'      => 'localhost',
      'username'  => 'root',
      'password'	=> 'root',
      'database'	=> 'rat',
      'prefix'	  => ''
		),
    'live' => array(
      'host'	  	=> 'localhost',
      'username'  => 'root',
      'password'	=> 'root',
      'database'	=> 'rat',
      'prefix'	  => ''
    )
  );

}