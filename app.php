<?php

class application {
	
	public $uri, $config;
	
	function __construct($uri = NULL, $config = NULL) {
		
		$this->uri = $uri;
		$this->config = $config;
		
		//// Loop from php.net. Stopped trying to get this loop to work in order to try and get a single model to load...
		//$handle = opendir('models');
		//while (false !== ($file = readdir($handle))) {
		//	$model = substr($file, 0, -4);
		//	if ($file[0] != '.') {
		//		$this->loadModel($model);
		//	}
		//}

		//// ...which proved difficult!
		//require 'models/user.php'; // << This line fails: "Cannot redeclare class user"
		//$this->user = new user;

		//// And yet this works ok:
		//$this->user = 'hi';
		
		// Stopping as I need to cook dinner!
		
	}
	
	function loadModel($model) {

		require 'models/'.$model.'.php';
		$this->$model = new $model;
		
	}
	
	function loadController($c) {
		
		require 'controllers/'.$c.'.php';
		
		$controller = new users($this->uri, $this->config);
		
		if (method_exists($controller, $this->uri['view'])) {
			$controller->{$this->uri['view']}($this->uri['params']);
		} else {
			$controller->index();
		}
		
				
	}

	function loadView($view) {

		global $app;

		require 'themes/'.$this->config->theme.'/'.$view.'.php';
		
	}
	
}

?>