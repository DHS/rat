<?php

class Routes {
	
	// Simple alias
	public $aliases = array(
		
		// Static pages
		
		'/' => array('controller' => 'items'),
		'/about' => array('controller' => 'pages', 'action' => 'show', 'id' => 'about'),
		'/contact' => array('controller' => 'pages', 'action' => 'show', 'id' => 'contact'),
		'/help' => array('controller' => 'pages', 'action' => 'show', 'id' => 'help'),
		'/404' => array('controller' => 'pages', 'action' => 'show', 'id' => '404'),
		
		// User functions
		
		'/signup' => array('controller' => 'users', 'action' => 'add'),
		'/login' => array('controller' => 'sessions', 'action' => 'add'),
		'/logout' => array('controller' => 'sessions', 'action' => 'remove'),
		'/settings' => array('controller' => 'users', 'action' => 'update'),
		
		// More complex
		
		// eg. /username
		'/*' => array('controller' => 'users', 'action' => 'show', 'id' => '$1'),
		// eg. /username/page
		'/*/*' => array('controller' => 'users', 'action' => 'show', 'id' => '$1', 'page' => '$2'),
		// eg. /username/item/item_id
		'/*/item/*' => array('controller' => 'items', 'action' => 'show', 'id' => '$2')
		
	);
	
}

?>