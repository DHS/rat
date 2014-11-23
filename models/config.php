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
  public $tagline             = 'Ratter is an app to demonstrate the functionality of <a href="http://github.com/DHS/rat">Rat</a>';
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
      'mime-types'  => 'image/jpeg,image/png,image/gif,image/pjpeg'
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
  public $admin_users = '1';

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
    self::fillObject($this, $raw_config);
    $this->processConfig();
  }

  static public function array_to_object($array) {
    // Casting input means you can actually pass in objects too
    $array = (array)$array;

    // Loop through array checking if values are arrays and converting those too
    foreach ($array as $key => $value) {
      if (is_array($value)) {
        $array[$key] = self::array_to_object($value);
      }
    }

    // Finally, cast top level to object and return
    return (object)$array;
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

  public static function fillObject($old_object, $new_object) {

    $new_object = self::array_to_object($new_object);

    foreach ($new_object as $key => $value) {
      $old_object->$key = $value;
    }

    if ($old_object != $this) {
      return $old_object;
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

  public function prepareConfigToWrite($posted_conf) {

    // Load existing config
    $conf = new Config;
    $conf = self::fillObject($conf, $posted_conf);

    // Overwrite checkbox fields
    //$checkboxes = array('beta', 'private', 'items["titles"]["enabled"]',
    //  'items["content"]["enabled"]', 'items["uploads"]["enabled"]',
    //  'items["comments"]["enabled"]', 'items["likes"]["enabled"]');
    $checkboxes = array(
      'beta', 'private', 'signup_email_notifications'
    );

    $conf->tagline = addslashes($conf->tagline);

    foreach ($checkboxes as $key => $checkbox) {
      if (isset($posted_conf->$checkbox) && $posted_conf->$checkbox == 'on') {
        $conf->$checkbox = 1;
      } else {
        $conf->$checkbox = 0;
      }
    }

    if (isset($posted_conf->items->titles->enabled) && $posted_conf->items->titles->enabled == 'on') {
      $conf->items->titles->enabled = 1;
    } else {
      $conf->items->titles->enabled = 0;
    }

    if (isset($posted_conf->items->content->enabled) && $posted_conf->items->content->enabled == 'on') {
      $conf->items->content->enabled = 1;
    } else {
      $conf->items->content->enabled = 0;
    }

    if (isset($posted_conf->items->uploads->enabled) && $posted_conf->items->uploads->enabled == 'on') {
      $conf->items->uploads->enabled = 1;
    } else {
      $conf->items->uploads->enabled = 0;
    }

    if (isset($posted_conf->items->comments->enabled) && $posted_conf->items->comments->enabled == 'on') {
      $conf->items->comments->enabled = 1;
    } else {
      $conf->items->comments->enabled = 0;
    }

    if (isset($posted_conf->items->likes->enabled) && $posted_conf->items->likes->enabled == 'on') {
      $conf->items->likes->enabled = 1;
    } else {
      $conf->items->likes->enabled = 0;
    }

    if (isset($posted_conf->invites->enabled) && $posted_conf->invites->enabled == 'on') {
      $conf->invites->enabled = 1;
    } else {
      $conf->invites->enabled = 0;
    }

    if (isset($posted_conf->friends->enabled) && $posted_conf->friends->enabled == 'on') {
      $conf->friends->enabled = 1;
    } else {
      $conf->friends->enabled = 0;
    }

    if (isset($posted_conf->friends->asymmetric) && $posted_conf->friends->asymmetric == 'on') {
      $conf->friends->asymmetric = 1;
    } else {
      $conf->friends->asymmetric = 0;
    }

    return $conf;

  }

  /**
   * Write config.json
   *
   */
  public function writeConfig($settings) {

    $config_file = $this->twig_string->render(
      file_get_contents("config/config.twig"),
      array('app' => array('config' => $settings))
    );

    $handle = fopen('config/config.json', 'w');
    fwrite($handle, $config_file);
    fclose($handle);

  }

}
