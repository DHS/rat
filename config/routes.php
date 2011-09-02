<?php

class Routes {
	
	// Simple alias
	public $aliases = array(
		
		'/signup' => array('controller' => 'users', 'action' => 'add'),
		'/login' => array('controller' => 'sessions', 'action' => 'add'),
		'/about' => array('controller' => 'pages', 'action' => 'show', 'id' => 'about'),
		'/contact' => array('controller' => 'pages', 'action' => 'show', 'id' => 'contact'),
		'/help' => array('controller' => 'pages', 'action' => 'show', 'id' => 'help'),
		'/*' => array('controller' => 'users', 'action' => 'show', 'params' => '$1'),
		'/*/item/*' => array('controller' => 'items', 'action' => 'show', 'params' => '$2')
		
	);
	
}

?>