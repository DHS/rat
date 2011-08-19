<?php

class Application {

	public $uri, $config;

	function __construct() {
		
		$this->loadConfig();
		$this->loadModels();
		$this->loadPlugins();
		
	}

	function request($uri) {
		
		$this->uri = $uri;
		
		$this->loadController($uri['controller']);

	}

	function loadConfig() {
		
		require_once 'config/config.php';
		$this->config = new Config;
	
		$domain = substr(substr($this->config->url, 0, -1), 7);

		if ($_SERVER['HTTP_HOST'] == $domain || $_SERVER['HTTP_HOST'] == 'www.'.$domain) {
			define('SITE_IDENTIFIER', 'live');
			$base_dir = $this->config->base_dir;
		} else {
			define('SITE_IDENTIFIER', 'dev');
			$base_dir = $this->config->dev_base_dir;
		}

		if (is_null($base_dir))
			$base_dir = '/';
		
		define('BASE_DIR', $base_dir);
		
	}

	function loadModels() {
	
		$handle = opendir('models');	
		while (false != ($file = readdir($handle))) {
			$model = substr($file, 0, -4);
			if ($file[0] != '.') {
				require_once "models/$model.php";
				$modelLower = strtolower($model);
        $this->$modelLower = new $model;
			}
		}
		
	}

	function loadPlugins() {
		
		foreach ($this->config->plugins as $key => $value) {
			if ($value == TRUE) {
				require_once "plugins/$key.php";
				$this->plugins->$key = new $key;
			}
		}

	}

	function loadController($c) {
 
		global $app;

		$classname = ucfirst($c).'Controller';
		require_once "controllers/$classname.php";
		$controller = new $classname;
		
		if (method_exists($controller, $this->uri['action'])) {
			$controller->{$this->uri['action']}($this->uri['id']);
		} else {
			$controller->index();
		}
		
	}

	function loadView($view) {
		
		global $app;
		
		include "themes/{$this->config->theme}/{$view}.php";
		
	}
	
	function loadLayout($view, $layout = NULL) {
		
		global $app;
		
		if (is_null($layout))
			$layout = 'default';
		
		include "themes/{$this->config->theme}/layouts/{$layout}.php";
		
	}
	
	function loadPartial($partial) {
		
		global $app;	
		
		include "themes/{$this->config->theme}/partials/{$partial}.php";
		
	}

	function link_to($value, $controller, $action = "", $id = "") {
	
		$link = "<a href='" . BASE_DIR . "/{$controller}";

		if (! empty($action))
			$link .= "/{$action}";
			
		if (! empty($id))
			$link .= "/{$id}";
		
		$link .= "'>{$value}</a>";
		
		return $link;
		
  }

}

?>