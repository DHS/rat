<?php

class Config {

  private $_config_path = 'config/config.json';

  public function __construct() {

    // Fetch config from config.json
    $raw_config = $this->loadConfigFile();

    // overwrite declared vars with those loaded from config.json
    self::fillObject($this, $raw_config);

    // Process config to setup base_dir and url
    $this->processConfig();
  }

  /**
   * Convert an array to an object, return the object
   *
   */
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

  /**
  * Load a config file
  *
  */
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

  /**
  * Overwrite an old object with properties from a new object
  *
  */
  public static function fillObject($old_object, $new_object = null) {

    if ($new_object != null) {

      $new_object = self::array_to_object($new_object);

      foreach ($new_object as $key => $value) {
        $old_object->$key = $value;
      }

      if ($old_object != $this) {
        return $old_object;
      }

    } else {

      // No new object given so create an object from the old one
      return self::array_to_object($old_object);

    }
  }

  /**
  * Process config file setting up some extra config settings
  *
  */
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

    // Create array of admin_users
    $this->admin_users = explode(',', $this->admin_users);

  }

  /**
  * Prepare a new config from the admin section ready to write to a config file
  *
  */
  public function prepareConfigToWrite($posted_conf) {

    // Load existing config
    $conf = new Config;

    // Convert posted conf to object
    $posted_conf = self::array_to_object($posted_conf);

    // Escape tagline
    $posted_conf->tagline = addslashes($posted_conf->tagline);

    // Setup new config
    $conf = self::fillObject($conf, $posted_conf);

    // Overwrite checkbox fields
    $posted_checkboxes = array(
      &$posted_conf->beta,
      &$posted_conf->private,
      &$posted_conf->signup_email_notifications,
      &$posted_conf->items->titles->enabled,
      &$posted_conf->items->content->enabled,
      &$posted_conf->items->uploads->enabled,
      &$posted_conf->items->comments->enabled,
      &$posted_conf->items->likes->enabled,
      &$posted_conf->invites->enabled,
      &$posted_conf->friends->enabled,
      &$posted_conf->friends->asymmetric,
    );

    $conf_checkboxes = array(
      &$conf->beta,
      &$conf->private,
      &$conf->signup_email_notifications,
      &$conf->items->titles->enabled,
      &$conf->items->content->enabled,
      &$conf->items->uploads->enabled,
      &$conf->items->comments->enabled,
      &$conf->items->likes->enabled,
      &$conf->invites->enabled,
      &$conf->friends->enabled,
      &$conf->friends->asymmetric,
    );

    $i = 0;
    foreach ($posted_checkboxes as $key => $checkbox) {
      if ($checkbox == 'on') {
        $conf_checkboxes[$i] = 1;
      } else {
        $conf_checkboxes[$i] = 0;
      }
      $i++;
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
