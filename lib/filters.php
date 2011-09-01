<?php

class Filters {
	
	public static function requireLoggedIn() {
		
		Application::flash('error', 'You must log in to do this!');
		//Application::redirect_to('sessions', 'add');
		
	}
	
	public static function requireLoggedOut() {
		
		Application::flash('error', 'You must logged out to do this!');
		//Application::redirect_to('items', 'index');
		
	}
	
}

?>