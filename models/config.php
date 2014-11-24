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
