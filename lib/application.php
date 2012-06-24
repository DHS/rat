<?php

class Application {

  public $uri, $config;

  private function __construct() {}

  public static function initialise() {

    // Check for server config
    if ( ! file_exists('config/server.php')) {
      throw new ApplicationException(null, "Server config doesn't exist yet. Copy config/server-sample.php to config/server.php and update the variables.");
    }

    require_once 'config/server.php';
    require_once 'config/application.php';
    $config = new AppConfig;

    require_once 'lib/routing.php';

    try {

      $uri = Routing::fetch_uri($config);

      $controller = ucfirst($uri['controller']).'Controller';
      @include "controllers/{$uri['controller']}_controller.php";

      if (substr($uri['action'], 0, 1) == '?') {
        $uri['action'] = '';
      }

      if (empty($uri['action']) && method_exists($controller, 'index')) {
        $uri['action'] = 'index';
      }

      // If controller found and action exists
      if (class_exists($controller) && method_exists($controller, $uri['action'])) {

        $app = new $controller;

      } else {

        $uri = Routing::route();

        $controller = ucfirst($uri['controller']).'Controller';
        @include "controllers/{$uri['controller']}_controller.php";

        $app = new $controller;

      }

      $app->loadConfig($config);
      $app->loadTwig();
      $app->loadModels();
      $app->loadPlugins();

      $app->uri = $uri;

      // Helper var to simplify reponding to json
      $app->json = $app->uri['format'] == 'json';

      require_once 'lib/filter.php';
      $app->runFilters();

      $app->loadDefaultLibs();

      // Set timezone from config
      date_default_timezone_set($config->timezone);

      // Call relevant function in controller
      $app->loadAction();

      unset($_SESSION['flash']);

    } catch (ValidationException $e) {

      ob_end_clean();

      Application::flash('error', $e->getMessage());
      header('Location: ' . $_SERVER['HTTP_REFERER']);

    } catch (RoutingException $e) {

      ob_end_flush();

      // RoutingExceptions only thrown from static context
      // so must set up new Application before rendering 404
      $app = new Application;
      $app->loadConfig($config);
      $app->loadView('pages/404');

    } catch (ApplicationException $e) {

      ob_end_flush();
      $e->app->loadView('pages/500');

    }

  }

  private function loadConfig($config) {

    $this->config = $config;

    // Work out the live domain from config
    $domain = substr($this->config->url, 7);

    // Determine if site is live or dev, set site_identifier constant and base_dir
    if ($_SERVER['HTTP_HOST'] == $domain || $_SERVER['HTTP_HOST'] == 'www.' . $domain) {
      define('SITE_IDENTIFIER', 'live');
      $base_dir = $this->config->base_dir;
    } else {
      define('SITE_IDENTIFIER', 'dev');
      $base_dir = $this->config->dev_base_dir;
    }

    // Add trailing slash if necessary
    if (substr($base_dir, -1) != '/') {
      $base_dir = $base_dir.'/';
    }

    // Set base_dir constant
    $this->config->base_dir = $base_dir;

    // Remove this eventually
    define('BASE_DIR', $base_dir);

    // Update config->url and append base_dir
    if (SITE_IDENTIFIER == 'live') {
      $this->config->url .= $base_dir;
    } else {
      $this->config->url = $this->config->dev_url . $base_dir;
    }

  }

  private function loadTwig() {

    $twig_config['cache'] = 'static/template_cache';

    // If we're in dev mode then force template compiling
    if (SITE_IDENTIFIER == 'dev') {
      $twig_config['auto_reload'] = TRUE;
    }

    require_once 'lib/Twig/Autoloader.php';
    Twig_Autoloader::register();
    $this->twig = new Twig_Environment(new Twig_Loader_Filesystem('themes/' . $this->config->theme), $twig_config);

    // Load a separate instance of twig to handle strings
    $this->twig_string = new Twig_Environment(new Twig_Loader_String(), $twig_config);

  }

  public function writeConfig($file, $settings = array()) {

    $config_file = $this->twig_string->render(file_get_contents("config/$file.twig"), array('app' => array('config' => $settings)));

    $handle = fopen("config/$file.php", 'w');
    fwrite($handle, $config_file);
    fclose($handle);

  }

  private function loadModels() {

    global $mysqli;

    require_once 'lib/mysql.php';

    $models = glob('models/*.php' );
    foreach ($models as $model) {
      require_once $model;
    }

  }

  private function loadPlugins() {

    $this->plugins = new StdClass();
    foreach ($this->config->plugins as $key => $value) {
      if ($value == TRUE) {
        require_once "plugins/$key.php";
        $this->plugins->$key = new $key;
      }
    }

  }

  private function runFilters() {

    $uri = $this->uri;

    $reflect = new ReflectionClass($this);

    foreach ($reflect->getProperties(ReflectionProperty::IS_PROTECTED) as $filter) {
      $filter_name = $filter->getName();
      $filter = new Filter($this);
      $filter->$filter_name($uri, $this->$filter_name);
    }

  }

  private function loadDefaultLibs() {

    require_once 'lib/content.php';
    require_once 'lib/email.php';
    $this->email = new Email($this);

  }

  private function loadAction() {

    // Check for params
    if (isset($this->uri['params'])) {
      $param_index = array_keys($this->uri['params']);
      $param_index = $param_index[0];
    } else {
      $param_index = null;
    }

    // Check that action method exists
    if (method_exists($this, $this->uri['action'])) {

      if (isset($this->uri['params']['id'])) {
        // Call the action method passing in id
        $this->{$this->uri['action']}($this->uri['params']['id']);
      } else {
        // Call the action method without params
        $this->{$this->uri['action']}();
      }

    } elseif (empty($this->uri['action']) && method_exists($this, 'index')) {

      $this->index($this->uri['params'][$param_index]);

    } else {

      throw new RoutingException($uri, "Page not found");

    }

  }

  protected function loadView($view, $params = array(), $layout = 'default') {

    // Note: the following is hardcoded in ajax methods
    $params['view'] = $view;
    $params['app'] = $this;
    $params['session'] = $_SESSION;

    if ($layout == 'admin') {
      $params['title'] = 'Admin';
    }

    // Hacks for user menu in header
    if (class_exists('User')) {
      $params['user_menu_enabled'] = true;
      if (isset($_SESSION['user_id'])) {
        $params['viewer'] = User::get_by_id($_SESSION['user_id']);
      }
    }

    echo $this->twig->render("layouts/{$layout}.html", $params);

  }

  public static function url_for($controller, $action = '', $id = '', $params = array()) {
    return Routing::url_for($controller, $action, $id, $params);
  }

  // url_for wrapper for use with Twig
  public function echo_url_for($controller, $action = '', $id = '', $params = array()) {
    echo $this->url_for($controller, $action, $id, $params);
  }

  public function url_for_route($route, array $params) {

    foreach ($params as $param) {
      $route = implode($param, explode('*', $route, 2));
    }

    return substr(BASE_DIR, 0, -1) . $route;

  }

  public function link_to($link_text, $controller, $action = '', $id = '', $params = array()) {
    echo '<a href="' . $this->url_for($controller, $action, $id, $params).'">' . $link_text.'</a>';
  }

  public function get_link_to($link_text, $controller, $action = '', $id = '', $params = array()) {
    return '<a href="' . $this->url_for($controller, $action, $id, $params).'">' . $link_text.'</a>';
  }

  public function redirect_to($controller, $action = '', $id = '', $params = array()) {
    header('Location: ' . $this->url_for($controller, $action, $id, $params));
  }

  public static function flash($category, $message) {

    if ( ! in_array($category, array('error', 'notice', 'success'))) {
      $category = 'success';
    }

    $_SESSION['flash'] = array('category' => $category, 'message' => $message);

  }

  public function render_json($ref) {

    if (is_array($ref)) {

      foreach ($ref as $r) {
        unset($r->user);
      }

    } else {

      unset($ref->user);

    }

    echo json_encode($ref);

  }

}
