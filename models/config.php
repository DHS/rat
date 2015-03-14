<?php

class Config {

  private $_config_path = 'config/config.json';

  /**
   * The prefix to look for when loading environment variables from config
   */
  private $_env_var_prefix = 'ENV::';

  public function __construct() {

    /**
     * Optional local environment vars to help replicate a production
     * environment such as Heroku
     *
     * In config/config.json use the prefix ENV:: before a value to load a
     * given environment variable ie. set the dev database host to
     * "ENV::MYSQL_HOST" to load the MYSQL_HOST environment variable.
     *
     */
    // putenv('MYSQL_HOST=localhost');
    // putenv('MYSQL_USER=root');
    // putenv('MYSQL_PASSWORD=');
    // putenv('MYSQL_DATABASE=rat');
    // putenv('ENCRYPTION_SALT=hw9e46');
    // putenv('AWS_ACCESS_KEY_ID=abc');
    // putenv('AWS_SECRET_ACCESS_KEY=123');
    // putenv('AWS_S3_BUCKET=rat-uploads');

    // Load database config from config.json
    self::fillObject($this, $this->loadDbConfigFile());

    // Determine environment
    $this->processConfigBeforeDb();

    // Load application config from database
    self::fillObject($this, $this->loadConfigFromDb());

    // Determine environment
    $this->processConfigAfterDb();

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
  private function loadDbConfigFile() {

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

      // Set new attributes on old object
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
  private function processConfigBeforeDb() {

    try {

      // Loop through environments
      foreach ($this->environments as $environment_name => $environment) {

        // Snip http from the beginning of the environment url
        $domain = substr($environment->url, 7);

        // Check if the current url matches the environment
        if ($_SERVER['HTTP_HOST'] == $domain
          || $_SERVER['HTTP_HOST'] == 'www.' . $domain) {

          // Set up some environment-specific config vars
          $this->site_identifier = $environment_name;
          $this->base_dir = $environment->base_dir;

          // Check db for environment variables
          foreach ($environment->database as $key => &$value) {
            $config_value_array = explode($this->_env_var_prefix, $value);
            if (count($config_value_array) > 1){
              // Exploding worked
              $environment->database->$key = getenv($config_value_array[1]);
            }
          }
          unset($value);

          break;

        }

      }

      // If site_identifier didn't get set then we have a problem
      if ($this->site_identifier == null) {
        throw new Exception('Environment could not be determined. Check the
        environment url you\'re running on matches one of those in
        config.json.');
      }

    } catch (Exception $e) {}

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

  /**
   * Process config file setting up some extra config settings
   */
  private function processConfigAfterDb() {

    // Check encryption salt for environment variables
    $config_value_array = explode($this->_env_var_prefix, $this->encryption_salt);
    if (count($config_value_array) > 1){
      // Exploding worked
      $this->encryption_salt = getenv($config_value_array[1]);
    }

    // Check for AWS S3 bucket environment variable
    $config_value_array = explode($this->_env_var_prefix, $this->config->items->uploads->aws_s3_bucket);
    if (count($config_value_array) > 1){
      // Exploding worked
      $this->config->items->uploads->aws_s3_bucket = getenv($config_value_array[1]);
    }

    // Create array of admin_users
    $this->admin_users = explode(',', $this->admin_users);

    // Create array of upload mime types
    $this->items->uploads->mime_types = explode(',', $this->items->uploads->mime_types);

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

  // Fetch config from database
  public function loadConfigFromDb() {

    global $mysqli;

    // Load own mysql connection as config is often loaded statically
    $db = $this->environments->{$this->site_identifier}->database;

    // Create database connection
    $mysqli = new mysqli(
      $db->host,
      $db->username,
      $db->password,
      $db->database
    );

    $sql = "SELECT * FROM `{$this->environments->{$this->site_identifier}->database->prefix}config` WHERE `id` = 1";

    $query = mysqli_query($mysqli, $sql);
    $result = mysqli_fetch_array($query, MYSQL_ASSOC);

    $conf = new stdClass();
    foreach ((array)$result as $key => $value) {
      if (is_object(json_decode($value)) == true) {
        $conf->$key = json_decode($value);
      } else {
        $conf->$key = $value;
      }
    }

    return $conf;
  }

}
