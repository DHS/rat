<?php

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class Application {

  public $uri, $config, $s3;

  public static function initialise() {

    require_once 'vendor/autoload.php';

    try {

      require_once 'lib/routing.php';
      $uri = Routing::fetch_uri();

      $controller = ucfirst($uri['controller']) . 'Controller';
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

        $controller = ucfirst($uri['controller']) . 'Controller';
        include "controllers/{$uri['controller']}_controller.php";

        $app = new $controller;

      }

      $app->loadConfig();
      $app->loadTwig();
      $app->loadAws();
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
      $app->loadView('pages/404');

    } catch (ApplicationException $e) {

      ob_end_flush();
      $e->app->loadView('pages/500');

    }

  }

  /**
   * Set up config
   */
  public function loadConfig() {
    require_once 'models/config.php';
    $this->config = new Config();
  }

  /**
  * Initialise Twig
  */
  public function loadTwig() {

    $twig_config['cache'] = 'static/template_cache';

    // If we're in dev mode then force template compiling
    if ($this->config->site_identifier == 'dev') {
      $twig_config['auto_reload'] = TRUE;
    }

    $this->twig = new Twig_Environment(new Twig_Loader_Filesystem('themes/' . $this->config->theme), $twig_config);

    // Load a separate instance of twig to handle strings
    $this->twig_string = new Twig_Environment(new Twig_Loader_String(), $twig_config);

  }

  /**
  * Initialise AWS
  */
  public function loadAws() {

    // Instantiate an S3 client
    $this->s3 = S3Client::factory();

  }

  /**
  * Initialise all the models in the /models folder
  */
  private function loadModels() {

    global $mysqli;

    require_once 'lib/mysql.php';

    $models = glob('models/*.php' );
    foreach ($models as $model) {
      require_once $model;
    }

  }

  /**
  * Initialise all the plugins in the /plugins folder
  */
  private function loadPlugins() {

    $this->plugins = new StdClass();
    foreach ($this->config->plugins as $key => $value) {
      if ($value == TRUE) {
        require_once "plugins/$key.php";
        $this->plugins->$key = new $key;
      }
    }

  }

  /**
  * Apply url filters
  */
  private function runFilters() {

    $uri = $this->uri;

    $reflect = new ReflectionClass($this);

    foreach ($reflect->getProperties(ReflectionProperty::IS_PROTECTED) as $filter) {
      $filter_name = $filter->getName();
      $filter = new Filter($this);
      $filter->$filter_name($uri, $this->$filter_name);
    }

  }

  /**
  * Initialise all default libs
  */
  private function loadDefaultLibs() {

    require_once 'lib/content.php';
    require_once 'lib/email.php';
    $this->email = new Email($this);

  }

  /**
   * Runs the appropriate method in the controller
   */
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

  /**
   * Renders the Twig template for the given view
   */
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

  /**
   * Generate a url for a given controller/action/id
   */
  public static function url_for($controller, $action = '', $id = '', $params = array()) {
    return Routing::url_for($controller, $action, $id, $params);
  }

  /**
   * Echo a url for a given controller/action/id
   */
  public function echo_url_for($controller, $action = '', $id = '', $params = array()) {
    echo $this->url_for($controller, $action, $id, $params);
  }

  /**
  * Generate a url for a given route
  */
  public function url_for_route($route, array $params) {

    foreach ($params as $param) {
      $route = implode($param, explode('*', $route, 2));
    }

    return substr($this->config->base_dir, 0, -1) . $route;

  }

  /**
  * Echos an html link for a given controller/action/id
  */
  public function link_to($link_text, $controller, $action = '', $id = '', $params = array()) {
    echo '<a href="' . $this->url_for($controller, $action, $id, $params) . '">' . $link_text . '</a>';
  }

  /**
  * Return an html link for a given controller/action/id
  */
  public function get_link_to($link_text, $controller, $action = '', $id = '', $params = array()) {
    return '<a href="' . $this->url_for($controller, $action, $id, $params) . '">' . $link_text . '</a>';
  }

  /**
  * Redirect to a given controller/action/id
  */
  public function redirect_to($controller, $action = '', $id = '', $params = array()) {
    header('Location: ' . $this->url_for($controller, $action, $id, $params));
  }

  /**
  * Add a flash message to the session, probably to be shown on the next page
  */
  public static function flash($category, $message) {

    if ( ! in_array($category, array('error', 'notice', 'success'))) {
      $category = 'success';
    }

    $_SESSION['flash'] = array('category' => $category, 'message' => $message);

  }

  /**
  * Render a json object when returning API objects
  */
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
