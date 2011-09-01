<?php

class Application {
	
	public $uri, $config;
	
	private function __construct() {}
	
	public static function initialise() {
		
		try {
			
			require_once 'config/server.php';
			require_once 'config/application.php';
			
			$config = new AppConfig;
			
			$uri = Application::fetch_uri($config);
			
			$controller = ucfirst($uri['controller']).'Controller';
			@include "controllers/{$uri['controller']}_controller.php";

			if (class_exists($controller) && (method_exists($controller, $uri['action'])) 
			|| (empty($uri['action']) && method_exists($controller, 'index'))) {
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

			//require_once 'lib/filters.php';
			//$app->runFilters();
			
			$app->loadAction();

			unset($_SESSION['flash']);
			
		} catch (ValidationException $e) {
			
			ob_end_clean();
			Application::flash('error', $e->getMessage());
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			
		} catch (RoutingException $e) {
			
			ob_end_flush();
			include 'static/404.html';
			
		} catch (ApplicationException $e) {
			
			ob_end_flush();
			include 'static/500.html';
			
		} catch (Exception $e) {
			
			ob_end_flush();
			include 'static/500.html';
			
		}
		
	}
	
	private static function fetch_uri($config) {
		
		// Get request from server, split into segments, store as controller, view, id and params
		$request = substr($_SERVER['REQUEST_URI'], (strlen($_SERVER['PHP_SELF']) - 10));
		
		// Split at '.' and before '?' to obtain request format
		$segments = preg_split("/\./", $request);
		$format = preg_split("/\?/", $segments[1]);
		$format = $format[0];
		
		// Split request at each '/' to obtain route
		$segments = preg_split("/\//", $segments[0]);
		
		// Set up uri variable to pass to app
		$uri = array(	'controller'	=> $segments[1],
						'action'		=> $segments[2],
						'format'		=> $format,
						'params'		=> $_GET
					);

		$uri['params']['id'] = $segments[3];

		// Set the controller to the default if not in URI
		if (empty($uri['controller'])) {
			$uri['controller'] = $config->default_controller;
		}
		
		return $uri;
		
	}

	private static function route() {
		
		require_once 'config/routes.php';

		$routes = new Routes;
		
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
			if (preg_match('|^'.$k.'$|', $request, $matches)) {

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
		
		$domain = substr(substr($this->config->url, 0, -1), 7);
		
		if ($_SERVER['HTTP_HOST'] == $domain || $_SERVER['HTTP_HOST'] == 'www.'.$domain) {
			define('SITE_IDENTIFIER', 'live');
			$base_dir = $this->config->base_dir;
		} else {
			define('SITE_IDENTIFIER', 'dev');
			$base_dir = $this->config->dev_base_dir;
		}

		if (is_null($base_dir)) {
			$base_dir = '/';
		}
		
		define('BASE_DIR', $base_dir);
		
	}
	
	private function loadModels() {
		
		require_once 'lib/mysql.php';
		
		$models = glob('models/*.php' );
		foreach ($models as $model) {
			require_once $model;
		}
				
	}
	
	private function loadPlugins() {
		
		foreach ($this->config->plugins as $key => $value) {
			if ($value == TRUE) {
				require_once "plugins/$key.php";
				$this->plugins->$key = new $key;
			}
		}
		
	}

	private function runFilters() {

		// Save uri to a new variable for use inside this function
		$uri = $this->uri;
		
		// If uri action is missing, assume index
		if (is_null($uri['action'])) {
			$uri['action'] = 'index';
		}
		
		// Instantiate Filters class
		$filters = new Filters($this);
		
		// Create a reflection of the Application class (controller file was included earlier)
		$reflect = new ReflectionClass(ucfirst($uri['controller']).'Controller');
		
		// Loop through protected variables of Application class looking for things like:
		// protected $requireLoggedIn = array('add', 'remove');
		foreach ($reflect->getProperties(ReflectionProperty::IS_PROTECTED) as $filter) {
			
			// Grab the name of the variable ie. requireLoggedIn
			$filter_name = $filter->getName();
			
			// This was my original idea but grabbing the property's value isn't as easy as this
			
			//// Check to see if uri action is in the list of actions that require filtering
			//if (in_array($uri['action'], $filter)) {
			//	
			//	// Match found! So call the relevant method in the Filters class
			//	$filter->$filter_name();
			//	
			//}
			
			// And it's probably going to involve using something like this inside the loop
			
			//$property = $reflect->getProperty($filter_name);
			//$property->setAccessible(true);
			//echo $reflect->getProperty($filter_name)->getValue();
			
			// The answer is here somewhere: http://php.net/manual/en/class.reflectionclass.php
			
		}
		
	}
	
	private function loadAction() {

		if (method_exists($this, $this->uri['action'])) {
			$this->{$this->uri['action']}($this->uri['params']['id']);
		} elseif (empty($this->uri['action']) && method_exists($this, 'index')) {
			$this->index($this->uri['params']['id']);
		} else {
			throw new RoutingException($uri, "Page not found");
		}

	}
	
	protected function loadView($view, $layout = NULL) {
		
		if (is_null($layout)) {
			$layout = 'default';
		}
		
		include "themes/{$this->config->theme}/layouts/{$layout}.php";
		
	}
	
	protected function loadPartial($partial) {
		
		include "themes/{$this->config->theme}/partials/{$partial}.php";
		
	}
	
	public function url_for($controller, $action = '', $id = '') {
	
		$url = BASE_DIR . "/{$controller}";
		
		if (!empty($action)) {
			$url .= "/$action";
		}
		
		if (!empty($id)) {
			$url .= "/$id";
		}

		return $url;

	}
	
	public function link_to($link_text, $controller, $action = '', $id = '') {
		
		return '<a href="'.$this->url_for($controller, $action, $id).'">'.$link_text.'</a>';
					
	}
	
	public function redirect_to($controller, $action = '', $id = '') {
		
		header('Location: ' . $this->url_for($controller, $action, $id));
		
	}
	
	public static function flash($category, $message) {
		
		$_SESSION['flash'] = array('category' => $category, 'message' => $message);
		
	}
	
}

?>
