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
			include "controllers/{$uri['controller']}_controller.php";

			if (class_exists($controller)) {
				$app = new $controller;
			} else {
				Application::checkAliases();
				//throw new RoutingException($uri, "Page not found");
			}
		
			$app->loadConfig($config);
			$app->loadModels();
			$app->loadPlugins();
		
			$app->uri = $uri;
	
			$app->route();

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
						'id'			=> $segments[3],
						'format'		=> $format,
						'params'		=> $_GET
					);

		// Set the controller to the default if not in URI
		if (empty($uri['controller'])) {
			$uri['controller'] = $config->default_controller;
		}

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

	private static function checkAliases() {
		
		require_once 'config/routes.php';

		$routes = new Routes;
		
		// Get request from server and remove BASE_DIR
		$request = substr($_SERVER['REQUEST_URI'], (strlen($_SERVER['PHP_SELF']) - 10));

		foreach ($routes->aliases as $k => $v) {

			// Swap asterisks for valid regex
			$k = str_replace("*", "([a-zA-Z0-9]+)", $k);

			// Match the request against current route
			if (preg_match('|^'.$k.'$|', $request, $matches)) {
				
				// Assign components of $uri based on array in routes class
				foreach ($v as $k => $v) {

					// Convert $x to xth parameter
					if (strstr($v, "$")) {
						$i = substr($v, 1);
						$v = $matches[$i];
					}

					$uri[$k] = $v;
				}

				break;
			}	
		
		}
	
		return $uri;

	}
	
	private function route() {

			if (method_exists($this, $this->uri['action'])) {
				$this->{$this->uri['action']}($this->uri['id']);
			} elseif (empty($this->uri['action']) && method_exists($this, 'index')) {
				$this->index($this->uri['id']);
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
	
	public function url_for($controller, $action = "", $id = "") {
	
		$url = BASE_DIR . "/{$controller}";
		
		if (!empty($action)) {
			$url .= "/$action";
		}
		
		if (!empty($id)) {
			$url .= "/$id";
		}

		return $url;

	}

	public function link_to($link_text, $controller, $action = "", $id = "") {
		
		return '<a href="'.$this->url_for($controller, $action, $id).'">'.$link_text.'</a>';
					
	}

	public function redirect_to($controller, $action = "", $id = "") {
	
		header('Location: ' . $this->url_for($controller, $action, $id));
			
	}

	public static function flash($category, $message) {
	
		if (! in_array($category, array('error', 'notice', 'success'))) {
			$category = 'success';
		}

		$_SESSION['flash'] = array('category' => $category, 'message' => $message);
	
	}
	
}

?>
