<?php

class SessionsController extends Application {
	
	function add() {
		
		if ($_POST['email'] && $_POST['password']) {
			
			$user = User::get_by_email($_POST['email']);
			$encrypted_password = md5($_POST['password'].$this->config->encryption_salt);
			
			if ($user->password == $encrypted_password) {
				
				foreach ($user as $key => $value) {
					$user_array[$key] = $value;
				}
				$_SESSION['user'] = $user_array;
				
				// Log login
				if (isset($this->plugins->log)) {
					$this->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'login');
				}
				
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
		
		if (isset($_SESSION['user'])) {
			// do logout

			$user_id = $_SESSION['user']['id'];

			session_unset();
			session_destroy();

			// log logout
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($user_id, 'user', NULL, 'logout');
			}

			$_SESSION['user'] = array();

			$message = 'You are now logged out.';

			// Go forth!
			if (SITE_IDENTIFIER == 'live') {
				header('Location: '.$this->config->url.$this->config->default_controller.'/?message='.urlencode($message));
			} else {
				header('Location: '.$this->config->dev_url.$this->config->default_controller.'/?message='.urlencode($message));
			}
			
			exit();
			
		}
		
		$this->message = 'Nothing to see here';
		$this->loadView();
		
	}
	
}

?>