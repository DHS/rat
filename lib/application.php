<?php

// Define app class
class application {

  public $uri, $config;

	function __construct($uri = NULL) {
    
    $this->uri = $uri;
    
    $this->loadConfig();
    $this->loadModels();
    $this->loadPlugins();
	
  }

	function loadConfig() {
		
		require_once 'config/config.php';
		$this->config = new config;
	
    $domain = substr(substr($this->config->url, 0, -1), 7);

    if ($_SERVER['HTTP_HOST'] == $domain || $_SERVER['HTTP_HOST'] == 'www.'.$domain) {
      define('SITE_IDENTIFIER', 'live');
    } else {
      define('SITE_IDENTIFIER', 'dev');
    }
		
  }

  function loadModels() {
	  
    $handle = opendir('models');	
    while (false != ($file = readdir($handle))) {
			$model = substr($file, 0, -4);
      if ($file[0] != '.') {
				require_once "models/$model.php";
				$this->$model = new $model;
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
    
    require_once "controllers/{$c}.php";

    $controller = new $c;

    if (method_exists($controller, $this->uri['view'])) {
      $controller->{$this->uri['view']}($this->uri['params']);
    } else {
      $controller->index();
    }

  }

	function loadView($view, $layout = NULL) {
    
    global $app;

    if (is_null($layout)) { $layout = 'default'; };
    include "themes/{$this->config->theme}/layouts/{$layout}.php";
		
	}
	
	function loadPartial($partial) {
	  
    global $app;	
    
    include "themes/{$this->config->theme}/partials/{$partial}.php";
		
	}

  public function isPublic() {
    
    return TRUE;
  
  }

}

?>
