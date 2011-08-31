<?php

class SessionsController extends Application {
	
	function add() {
		
		if (isset($_POST['email']) && isset($_POST['password'])) {
			
			$user = User::get_by_email($_POST['email']);
			
			if ($user->authenticate($_POST['password']) == TRUE) {
				
				// Get redirected
				if (isset($_GET['redirect_to'])) {
					header('Location: '.$_GET['redirect_to']);
					exit();
				}
				
				// Go forth
				if (SITE_IDENTIFIER == 'live') {
					header('Location: '.$this->config->url);
				} else {
					header('Location: '.$this->config->dev_url);
				}
				
				exit();
				
			} else {
				
				$this->message .= 'Something isn\'t quite right.<br />Please try again...';
				$email = $_POST['email'];
				
			}
			
		}
		
		if (empty($_SESSION['user'])) {
			
			$this->title = 'Login';
			$this->loadView('sessions/add');
			
		} else {
			
			$this->message = 'You are already logged in!<br />';
			$this->message .= $this->link_to('Click here', 'sessions', 'remove').' to logout.';
			$this->loadView();
			
		}
		
	}
	
	function remove() {
		
		$user = User::get_by_id($_SESSION['user']['id']);
		
		if ($user->deauthenticate() == TRUE) {
			
			Application::flash('info', 'You are now logged out.');
			Application::redirect_to($this->config->default_controller);
			
		} else {
			
			Application::flash('info', 'Nothing to see here.');
			$this->loadView();
			
		}
		
	}
	
}

?>