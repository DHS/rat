<?php

class Config {

  private $_config_path = 'config/config.json';

  // URLs - must include http:// and no trailing slash
  public $url     = 'http://example.com';
  public $dev_url = 'http://127.0.0.1';

  // Base directory - the directory in which your site resides if not in the server root
  public $live_base_dir      = '/';
  public $dev_base_dir  = '/rat';

  // Email enabled - search project for "// Email user" to find what this affects
  public $send_emails = TRUE;

  // Encryption salt - change to a random six character string, do not change after first use of application
  public $encryption_salt = 'hw9e46';

  // Set timezone
  public $timezone = 'Europe/London';

  // Database
  public $database = array(
    'dev' => array(
      'host'      => 'localhost',
      'username'  => 'root',
      'password'  => '',
      'database'  => 'rat',
      'prefix'    => ''
    ),
    'live' => array(
      'host'      => 'localhost',
      'username'  => 'root',
      'password'  => 'root',
      'database'  => 'rat',
      'prefix'    => ''
    )
  );

  // Basic app variables
  public $name                = 'Ratter';
  public $tagline             = 'deave<a href="http://github.com/DHS/rat">Rat</a>';
  public $default_controller  = 'items';

  // Beta - users can't signup, can only enter their email addresses
  public $beta = TRUE;

  // Private app - requires login to view pages (except public_pages)
  // no share buttons
  public $private = FALSE;
  public $signup_email_notifications = TRUE;

  // Items
  // Notes about uploads: max-size is in bytes (default: 5MB), directory
  // should contain three subdirectories: originals, thumbnails, stream
  public $items = array(
    'name'        => 'post',
    'name_plural' => 'posts',

    'titles' => array(
      'enabled'     => TRUE,
      'name'        => 'Title',
      'name_plural' => 'Titles'
    ),

    'content' => array(
      'enabled'     => TRUE,
      'name'        => 'Content',
      'name_plural' => 'Contents'
    ),

    // Remember to update the permissions for your
    // upload dir e.g. chmod -R 777 uploads
    'uploads'       => array(
      'enabled'     => TRUE,
      'name'        => 'Image',
      'directory'   => 'uploads',
      'max-size'    => '5242880',
      'mime-types'  => array(
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/pjpeg'
      )
    ),

    'comments' => array(
      'enabled'     => TRUE,
      'name'        => 'Comment',
      'name_plural' => 'Comments'
    ),

    'likes' => array(
      'enabled'       => TRUE,
      'name'          => 'Like',
      'name_plural'   => 'Likes',
      'opposite_name' => 'Unlike',
      'past_tense'    => 'Liked by'
    )

  );

  // Invites system
  public $invites = array('enabled' => TRUE);

  // Friends - still testing, works with asymmetric set to true... just!
  // (Shows 'Follow' link & generates homepage feed)
  public $friends = array(
    'enabled'     => FALSE,
    'asymmetric'  => FALSE
  );

  // Admin users - array of user IDs who have access to admin area
  public $admin_users = array(1);

  // Theme
  public $theme = 'default';

  // Plugins
  public $plugins = array(
    'log'       => TRUE,
    'gravatar'  => TRUE,
    'points'    => FALSE,
    'analytics' => FALSE
  );

  // Send emails from what address?
  public $send_emails_from = 'support@blah.com';

  public $site_identifier;
  public $base_dir;

  public function __construct() {

    $raw_config = $this->loadConfigFile();
    $this->fillObject($raw_config);
    $this->processConfig();

  }

  private function loadConfigFile() {

    $config_contents = file_get_contents($this->_config_path);

    if ($config_contents === false) {
      throw new ConfigException($this->uri, 'Config file could not be read');
    }

    if ($config_contents == '') {
      throw new ConfigException($this->uri, 'Config file appears to be empty');
    }

    $decoded_config = json_decode($config_contents);

    if ($decoded_config == null) {
      throw new ConfigException($this->uri, 'Config file appears to contain invalid json');
    }

    return $decoded_config;

  }

  private function fillObject($raw_config) {

    foreach ($this as $key => $value) {
      $this->$key = $raw_config->$key;
    }

  }

  private function processConfig() {

    // Work out the live domain from config
    $domain = substr($this->url, 7);

    // Determine if site is live or dev, set site_identifier constant and base_dir
    if ($_SERVER['HTTP_HOST'] == $domain || $_SERVER['HTTP_HOST'] == 'www.' . $domain) {
      $this->site_identifier = 'live';
      $this->base_dir = $this->live_base_dir;
    } else {
      $this->site_identifier = 'dev';
      $this->base_dir = $this->dev_base_dir;
    }

    // Add trailing slash if necessary
    if (substr($this->base_dir, -1) != '/') {
      $this->base_dir = $this->base_dir.'/';
    }

    // Update config->url and append base_dir
    if ($this->site_identifier == 'live') {
      $this->url .= $this->base_dir;
    } else {
      $this->url = $this->dev_url . $this->base_dir;
    }

  }

}
