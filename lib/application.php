<?php

class Application {
	
	public $uri, $config;
	
	private function __construct() {}
	
	public static function initialise() {
		
		require_once 'config/server.php';
		require_once 'config/application.php';
			
		$config = new AppConfig;

		try {
						
			$uri = Application::fetch_uri($config);
			
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
				
				$uri = Application::route();
				
				$controller = ucfirst($uri['controller']).'Controller';
				@include "controllers/{$uri['controller']}_controller.php";
				
				$app = new $controller;
				
			}
			
			$app->loadConfig($config);
			$app->loadModels();
			$app->loadPlugins();
			
			$app->uri = $uri;
			
			// Helper var to simplify reponding to json
			$app->json = $app->uri['format'] == 'json';		
			
			require_once 'lib/filter.php';
			$app->runFilters();
			
			// Set timezone from config
			date_default_timezone_set($config->timezone);
			
			// Load twig
			if ($config->theme == 'twig') {
				
				$twig_config['cache'] = 'static/template_cache';
				
				// If we're in dev mode then force template compiling
				if (SITE_IDENTIFIER == 'dev') {
					$twig_config['auto_reload'] = TRUE;
				}
				
				require_once 'lib/twig/Autoloader.php';
				Twig_Autoloader::register();
				$loader = new Twig_Loader_Filesystem('themes/'.$config->theme);
				$app->twig = new Twig_Environment($loader, $twig_config);
				
			}
		
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
	
	private static function fetch_uri($config) {
		
		// Get request from server, split into segments, store as controller, view, id and params
		$request = substr($_SERVER['REQUEST_URI'], (strlen($_SERVER['PHP_SELF']) - 10));
		
		// Split at '.'
		$dot_split = preg_split("/\./", $request);
		
		// Grab format from after '.' but before '?'
		if (isset($dot_split[1])) {
			$format_split = preg_split("/\?/", $dot_split[1]);
			$format = $format_split[0];
		} else {
			$format = NULL;
		}
		
		// Split request at each '/' to obtain route (using everything before '.')
		$segments = preg_split("/\//", $dot_split[0]);
		
		// Set up uri variable to pass to app

		$uri['controller'] = $segments[1];
		
		if (isset($segments[2])) {
			$uri['action'] = $segments[2];
		} else {
			$uri['action'] = NULL;
		}
		
		$uri['format'] = $format;
		$uri['params'] = array_map('htmlentities', $_GET);
		
		if (isset($segments[3])) {
			$uri['params']['id'] = $segments[3];
		} else {
			$uri['params']['id'] = NULL;
		}
		
		// Set the controller to the default if not in URI
		if (empty($uri['controller'])) {
			$uri['controller'] = $config->default_controller;
		}
		
		return $uri;
		
	}
	
	private static function route() {
		
		require_once 'config/routes.php';
		$routes = new Routes();
		
		// Get request from server and remove BASE_DIR
		$request = substr($_SERVER['REQUEST_URI'], (strlen($_SERVER['PHP_SELF']) - 10));
		
		// Split at '.' and before '?' to obtain request format
		$request = preg_split("/\./", $request);
		$request = $request[0];
		$format = preg_split("/\?/", $request[1]);
		$format = $format[0];
		
		$routeFound = FALSE;
		
		foreach ($routes->aliases as $k => $v) {
			
			// Swap asterisks for valid regex
			$k = str_replace("*", "([a-zA-Z0-9]+)", $k);
			
			// Match the request against current route
			if (preg_match('|^'.$k.'$/?|', $request, $matches)) {
				
				$uri['controller'] = $v['controller'];
				$uri['action'] = $v['action'];
				
				// Assign components of $uri['params'] based on array in routes class
				foreach ($v as $k => $v) {
					
					if ($k != 'controller' && $k != 'action') {
						
						// Convert $x to xth parameter
						if (strstr($v, "$")) {
							$i = substr($v, 1);
							$v = $matches[$i];
						}
						
						$uri['params'][$k] = $v;
						
					}
					
				}
				
				$uri['format'] = $format;
				$routeFound = TRUE;
				break;
				
			}
			
		}
		
		if (! $routeFound) throw new RoutingException($uri, "Page not found");
		
		return $uri;
		
	}
	
	private function loadConfig($config) {
		
		$this->config = $config;
		
		// Work out the live domain from config
		$live_domain = substr(substr($this->config->url, 0, -1), 7);
		
		// Determine if site is live or dev, set site_identifier constant and base_dir
		if ($_SERVER['HTTP_HOST'] == $live_domain || $_SERVER['HTTP_HOST'] == 'www.'.$live_domain) {
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
		
		// Update config->url
		if (SITE_IDENTIFIER == 'live') {
			// config->url already setup
			if (BASE_DIR != '/') {
				// Append base_dir if needed
				$this->config->url .= BASE_DIR;
			}
		} else {
			if (BASE_DIR != '/') {
				$this->config->url = $this->config->dev_url . BASE_DIR;
			} else {
				// Dev site so set config->url to config->dev_url for use everywhere from now
				$this->config->url = $this->config->dev_url;
			}
		}
		
	}
	
	private function loadModels() {
		
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
	
	private function loadAction() {
		
		if (method_exists($this, $this->uri['action'])) {
			if (isset($this->uri['params']['id'])) {
				$this->{$this->uri['action']}($this->uri['params']['id']);
			} else {
				$this->{$this->uri['action']}();
			}
		} elseif (empty($this->uri['action']) && method_exists($this, 'index')) {
			$this->index($this->uri['params']['id']);
		} else {
			throw new RoutingException($uri, "Page not found");
		}
		
	}
	
	protected function loadView($view, $params = NULL, $layout = NULL) {
		
		if (is_null($layout)) {
			$layout = 'default';
		}
		
		if ($this->config->theme == 'twig') {
			
			$params['view'] = $view;
			$params['app'] = $this;
			$params['session'] = $_SESSION;

			// Hacks for user menu in header
			if (class_exists('User')) {
				$params['user_menu_enabled'] = true;
				if (isset($_SESSION['user_id'])) {
					$params['viewer'] = User::get_by_id($_SESSION['user_id']);
				}
			}
			
			echo $this->twig->render("layouts/{$layout}.html", $params);
			
		} else {
			
			include "themes/{$this->config->theme}/layouts/{$layout}.php";
			
		}
		
	}
	
	protected function loadPartial($partial) {
		
		include "themes/{$this->config->theme}/partials/{$partial}.php";
		
	}
	
	public function url_for($controller, $action = '', $id = '') {
		
		$uri_array = array('controller' => $controller);
		
		if (!empty($action)) {
			$uri_array['action'] = $action;
		}
		
		if (!empty($id)) {
			$uri_array['id'] = $id;
		}
		
		require_once 'config/routes.php';
		$routes = new Routes();
		
		if ($route = array_search($uri_array, $routes->aliases)) {
			
			// Routes all preceded by / so snip it as base_dir includes trailing /
			if (substr($route, 0, 1) == '/') {
				$route = substr($route, 1);
			}
			
			return BASE_DIR . $route;
			
		} else {
			
			$url = BASE_DIR . $controller;
			
			if (!empty($action)) {
				$url .= "/$action";
			}
			
			if (!empty($id)) {
				$url .= "/$id";
			}
			
			return $url;
			
		}
		
	}

	// url_for wrapper for use with Twig
	public function echo_url_for($controller, $action = '', $id = '') {
		echo $this->url_for($controller, $action, $id);
	}

	public function url_for_route($route, array $params) {

		foreach ($params as $param) {
			$route = implode($param, explode('*', $route, 2));
		}

		return substr(BASE_DIR, 0, -1) . $route;
		
	}
	
	public function link_to($link_text, $controller, $action = '', $id = '') {
		
		echo '<a href="'.$this->url_for($controller, $action, $id).'">'.$link_text.'</a>';
		
	}
	
	public function get_link_to($link_text, $controller, $action = '', $id = '') {
		
		return '<a href="'.$this->url_for($controller, $action, $id).'">'.$link_text.'</a>';
		
	}
	
	public function redirect_to($controller, $action = '', $id = '') {
		
		header('Location: ' . $this->url_for($controller, $action, $id));
		
	}
	
	public static function flash($category, $message) {
		
		if (! in_array($category, array('error', 'notice', 'success'))) {
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

?>