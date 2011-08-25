<?php

class UsersController extends Application {
	
	function __construct() {
		
		// Check if user is logged in and trying to signup
		if ($this->uri['action'] == 'add' && !empty($_SESSION['user'])) {

			$this->title = 'Signup';
			$this->message = 'You are already logged in!';
			$this->loadView('partials/header');
			$this->loadView('partials/footer');
			exit;

		}
		
	}
	
	// Show a list of users / not used
	function index() {
		
		// Not needed?
		
	}
	
	// Add a user / signup
	function add($code) {
		
		if (isset($_POST['email'])) {
			//User is trying to signup
			
			if (isset($code)){
				// User is signing up with a code
				
				$this->signup_code();
				
			} else {
				// User is signing up without a code 
				
				if ($this->config->beta == TRUE) {
					// Do beta signup
					
					$this->signup_beta();
					
				} else {
					// Do full signup
					
					$this->signup_full();
					
				}
				
			}
			
		} else {
			
			// No email submitted so show signup form
			
			if (isset($code) && $code != '') {
				
				$this->code = $code;
				$this->title = 'Signup';
				$this->loadLayout('users/add');
				
			} else {
				
				if ($this->config->beta == TRUE) {
					// Show beta signup form

					$this->title = 'Beta signup';
					$this->loadLayout('users/add_beta');

				} else {
					// Show full signup form

					$this->title = 'Signup';
					$this->loadLayout('users/add');

				}
				
			}
			
		}
		
	}
	
	// Show a user / user page
	function show($id) {
		
		$this->user = User::get_by_id($id);
		$this->items = $this->user->items();

		$this->title = $this->user->username;		
		$this->loadView('users/show');
		
	}
	
	// Update user: change passsword, update profile
	function update($page) {
		
		if (!isset($page)) {
			
			$page = 'password';
			
		} elseif ($page == 'password') {
			
			if (isset($_POST['old_password']) && isset($_POST['new_password1']) && isset($_POST['new_password2'])) {
				$this->update_password($this->config->encryption_salt);
			}
			
		} elseif ($page == 'profile') {
			
			if (isset($_POST['full_name']) || isset($_POST['bio']) || isset($_POST['url'])) {
				$this->update_profile();
			}
			
		}
		
		$this->user = User::get_by_id($_SESSION['user']['id']);

		$this->title = 'Settings';
		$this->loadLayout('users/update_'.$page);
		
	}
	
	// Password reset
	function reset($code) {
		
		if (isset($_SESSION['user'])) {
			
			$this->title = 'Page not found';
			$this->loadLayout();
			exit;
			
		}
		
		if (isset($code)) {
			// Process reset
			
			// If two passwords submitted then check, otherwise show form
			if (isset($_POST['password1']) && isset($_POST['password2'])) {
				
				if (User::check_password_reset_code($code) == FALSE) {
					exit();
				}
				
				if ($_POST['password1'] == '' || $_POST['password2'] == '') {
					$error .= 'Please enter your password twice.<br />';
				}
				
				if ($_POST['password1'] != $_POST['password2']) {
					$error .= 'Passwords do not match.<br />';
				}
				
				// Error processing
				if ($error == '') {
					
					$user_id = User::check_password_reset_code($code);
					
					// Do update
					User::update_password($user_id, $_POST['password1'], $this->config->encryption_salt);
					
					$user = User::get_by_id($user_id);
					
					// Start session
					foreach ($user as $key => $value) {
						$user_array[$key] = $value;
					}
					$_SESSION['user'] = $user_array;
					
					// Log login
					if (isset($this->plugins->log)) {
						$this->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'login');
					}
					
					// Set welcome message
					$this->message = urlencode('Password updated! Welcome back to '.$this->config->name.'!');
					
					// If redirect_to is set then redirect
					if (isset($_GET['redirect_to'])) {
						header('Location: '.$_GET['redirect_to'].'?message='.$this->message);
						exit();
					}
					
					// Go forth!
					if (SITE_IDENTIFIER == 'live') {
						header('Location: '.$this->config->url.$this->config->default_controller.'/?message='.$this->message);
					} else {
						header('Location: '.$this->config->dev_url.$this->config->default_controller.'/?message='.$this->message);
					}
					
					exit();
					
				} else {
					// Show error message
					
					$this->message = $error;
					$this->loadView('partials/header');
					if (User::check_password_reset_code($code) != FALSE) {
						$this->loadView('reset');
					}
					$this->loadView('partials/footer');
					
				}
				
			} else {
				// Code present so show password reset form
				
				if (User::check_password_reset_code($code) == TRUE) {
					// Invite code valid
					
					$this->code = $code;
					$this->loadLayout('users/reset');

				} else {
					
					$this->title = 'Page not found';
					$this->loadLayout();
					exit;
					
				}
				
			}
			
		} elseif (empty($_SESSION['user'])) {
			// No code in URL so show new reset form
			
			if (isset($_POST['email'])) {
				// Email submitted so send password reset email
				
				$user = User::get_by_email($_POST['email']);
				
				// Check is a user
				if ($user != NULL) {
					
					// Generate code
					$code = User::generate_password_reset_code($user->id);
					
					$to = $_POST['email'];
					$link = substr($this->config->url, 0, -1).$this->link_to(NULL, 'users', 'reset', $code);
					$headers = "From: {$this->config->name} <robot@blah.com>\r\nContent-type: text/html\r\n";
					
					// Load subject and body from template
					$this->loadView('emails/password_reset');
					
					// Email user
					if ($this->config->send_emails == TRUE) {
						mail($to, $subject, $body, $headers);
					}
					
				}
				
				$this->message = 'Check your email for instructions about how to reset your password!';
				$this->loadLayout();
				
			} else {
				
				$this->loadLayout('users/reset_new');
				
			}
			
		} else {
			
			$this->title = 'Page not found';
			$this->loadView('partials/header');
			$this->loadView('partials/footer');
			exit;
			
		}
		
	}
	
	// Confirm email address
	function confirm($email) {
		
		
		
	}
	
	// Show user profile in json format
	function json($username) {
		
		$user = User::get_by_username($username);
		
		$user['user'] = $user;
		$user['items'] = $user->items($user->id);
		$this->json = $user;
		$this->loadView('pages/json');
		
	}
	
	// Helper function: update password
	private function update_password($salt) {
		
		if (md5($_POST['old_password'].$salt) == $_SESSION['user']['password']) {
			// Check old passwords match
			
			if ($_POST['new_password1'] == $_POST['new_password2']) {
				// New passwords match
				
				// Call update_password in user model
				User::update_password($_SESSION['user']['id'], $_POST['new_password1'], $salt);
				
				// Update session
				$_SESSION['user']['password'] = md5($_POST['new_password1'].$salt);
				
				// Log password update
				if (isset($this->plugins->log)) {
					$this->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'change_password');
				}
				
				$this->message = 'Password udpated!';
				
			} else {
				// New passwords don't match
				
				$this->message = 'There was a problem, please try again.';
				
			}
			
		} else {
			// Old passwords don't match
			
			$this->message = 'There was a problem, please try again.';
			
		}
		
	}
	
	//  Helper function: update profile
	private function update_profile() {

		$error = '';

		// Validate URL
        
		// Check for empty URL. Default value: http://
		if ($_POST['url'] == 'http://') {
			$_POST['url'] = NULL;
		}
        
		// Ensure URL begins with http://
		if ($_POST['url'] != NULL && (substr($_POST['url'], 0, 7) != 'http://' && substr($_POST['url'], 0, 8) != 'https://')) {
			$_POST['url'] = 'http://'.$_POST['url'];
		}
        
		// Check for spaces
		if (User::check_contains_spaces($_POST['url']) == TRUE) {
			$error = 'URL cannot contain spaces.';
		}
        
		// End URL validation
        
		if ($error == '') {
        
			// Update session vars
			$_SESSION['user']['full_name'] = $_POST['full_name'];
			$_SESSION['user']['bio'] = $_POST['bio'];
			$_SESSION['user']['url'] = $_POST['url'];
			
			// Call user_update_profile in user model
			User::update_profile($_SESSION['user']['id'], $_POST['full_name'], $_POST['bio'], $_POST['url']);
        
			// Set success message
			$this->message = 'Profile information updated!';
        
		} else {
        
			$this->message = $error;
        
		}
        
	}
	
	// Helper function: signup with an invite code
	private function signup_code() {
		
		// Check invite code (only really matters if app is in beta)
		
		if ($this->config->beta == TRUE) {
			
			if (Invite::check_code_valid($_POST['code'], $_POST['email']) != TRUE) {
				$error .= 'Invalid invite code.<br />';
			}
			
		}
		
		// Check email
		$_POST['email'] = trim($_POST['email']);
		$email_check = $this->check_email($_POST['email']);
		if ($email_check !== TRUE) {
			$error .= $email_check;
		}
		
		// Check username
		$username_check = $this->check_username($_POST['username']);
		if ($username_check !== TRUE) {
			$error .= $username_check;
		}
		
		// Check password
		if ($_POST['password1'] == '' || $_POST['password2'] == '') {
			$error .= 'Please enter your password twice.<br />';
		}
		if ($_POST['password1'] != $_POST['password2']) {
			$error .= 'Passwords do not match.<br />';
		}
		
		// Error processing
		
		if ($error == '') {
			// No error so proceed...
			
			// First check if user added
			$user = User::get_by_email($_POST['email']);
			
			// If not then add
			if ($user == NULL) {
				$user_id = User::add($_POST['email']);
				$user = User::get_by_id($user_id);
			}
			
			// Do signup
			User::signup($user->id, $_POST['username'], $_POST['password1'], $this->config->encryption_salt);
            
			if ($this->config->send_emails == TRUE) {
				// Send 'thank you for signing up' email
            	
				$to = "{$_POST['username']} <{$_POST['email']}>";
				$headers = "From: David Haywood Smith <davehs@gmail.com>\r\nBcc: davehs@gmail.com\r\nContent-type: text/html\r\n";
            	
				// Load subject and body from template
				$this->loadView('email/signup');
            	
				// Email user
				mail($to, $subject, $body, $headers);
            	
			}
            
			// Log signup
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($user->id, 'user', NULL, 'signup');
			}
            
			// Start session
			foreach ($user as $key => $value) {
				$user_array[$key] = $value;
			}
			$_SESSION['user'] = $user_array;
            
			// Check invites are enabled
			if ($this->config->invites['enabled'] == TRUE) {
				
				// Get invites
				$invites = Invite::list_by_code($_POST['code']);
				
				if (is_array($invites)) {
					foreach ($invites as $invite) {
						
						// Update invites
						Invite::update($invite->id);
						
						// Log invite update
						if (isset($this->plugins->log)) {
							$this->plugins->log->add($_SESSION['user']['id'], 'invite', $invite->id, 'accept');
						}
						
						// Update points (but only if inviting user is not an admin)
						if (isset($this->plugins->points) && in_array($invite->user_id, $this->config->admin_users) != TRUE) {
							
							// Update points
							$this->plugins->points->update($invite->user_id, $this->plugins->points['per_invite_accepted']);
							
							// Log points update
							if (isset($this->plugins->log)) {
								$this->plugins->log->add($invite->user_id, 'points', NULL, $this->plugins->points['per_invite_accepted'], 'invite_accepted = '.$invite->id);
							}
							
						}
						
					}
					// end foreach
				}
				// end if is_array
            	
			}
            
			// Log login
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'login');
			}
            
			// If redirect_to is set then redirect
			if ($_GET['redirect_to']) {
				header('Location: '.$_GET['redirect_to']);
				exit();
			}
            
			// Set welcome message
			$this->message = urlencode('Welcome to '.$this->config->name.'!');
            
			// Go forth!
			if (SITE_IDENTIFIER == 'live') {
				header('Location: '.$this->config->url.$this->config->default_controller.'/?message='.$this->message);
			} else {
				header('Location: '.$this->config->dev_url.$this->config->default_controller.'/?message='.$this->message);
			}
            
			exit();
			
		} else {
			// There was an error
			
			// Propagate get vars to be picked up by the form
			$_GET['email']		= $_POST['email'];
			$_GET['username']	= $_POST['username'];
			$_GET['code']		= $_POST['code'];
			
			// Show error message
			$this->message = $error;
			$this->title = 'Signup';
			
			// Show signup form
			$this->loadLayout('users/add');
			
		}
		
	}
	
	// Helper function: beta signup
	private function signup_beta() {
		
		// Check email
		$_POST['email'] = trim($_POST['email']);
		$email_check = $this->check_email($_POST['email']);
		if ($email_check !== TRUE) {
			$error .= $email_check;
		}
		
		// Error processing
		
		if ($error == '') {
			// No error so proceed...
			
			// First check if user added
			$user = User::get_by_email($_POST['email']);
			
			// If not then add
			if ($user == NULL) {
				$user_id = User::add($_POST['email']);
				$user = User::get_by_id($user_id);
			}
			
			// Log beta signup
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($user_id, 'user', NULL, 'beta_signup', $_POST['email']);
			}
            
			// Set thank you & tweet this message
			$this->message = "Thanks for signing up!<br />We will be in touch soon...";
            
			// Go forth!
			if (SITE_IDENTIFIER == 'live') {
				header('Location: '.$this->config->url.$this->config->default_controller.'/?message='.urlencode($this->message));
			} else {
				header('Location: '.$this->config->dev_url.$this->config->default_controller.'/?message='.urlencode($this->message));
			}
            
			exit();
            
		} else {
			// There was an error
			
			// Propagate get vars to be picked up by the form
			$_GET['email']		= $_POST['email'];
			$_GET['username']	= $_POST['username'];
			$_GET['code']		= $_POST['code'];
			
			// Show error message
			$this->message = $error;
			$this->title = 'Beta signup';
			
			// Show signup form
			$this->loadLayout('users/add_beta');
			
		}
		
	}
	
	// Helper function: full signup
	private function signup_full() {
		
		// Check email
		$_POST['email'] = trim($_POST['email']);
		$email_check = $this->check_email($_POST['email']);
		if ($email_check !== TRUE) {
			$error .= $email_check;
		}

		// Check username
		$username_check = $this->check_username($_POST['username']);
		if ($username_check !== TRUE) {
			$error .= $username_check;
		}
		
		// Check password
		if ($_POST['password1'] == '' || $_POST['password2'] == '') {
			$error .= 'Please enter your password twice.<br />';
		}
		if ($_POST['password1'] != $_POST['password2']) {
			$error .= 'Passwords do not match.<br />';
		}
		
		// Error processing
		if ($error == '') {
			// No error so proceed...
			
			// First check if user added
			$user = User::get_by_email($_POST['email']);
			
			// If not then add
			if ($user == NULL) {
				$user_id = User::add($_POST['email']);
				$user = User::get_by_id($user_id);
			}
			
			// Do signup
			User::signup($user->id, $_POST['username'], $_POST['password1'], $this->config->encryption_salt);
            
			if ($this->config->send_emails == TRUE) {
				// Send 'thank you for signing up' email
				
				$to = "{$_POST['username']} <{$_POST['email']}>";
				$headers = "From: David Haywood Smith <davehs@gmail.com>\r\nBcc: davehs@gmail.com\r\nContent-type: text/html\r\n";
				
				// Load subject and body from template
				$this->loadView('email/signup');
				
				// Email user
				mail($to, $subject, $body, $headers);
				
			}
            
			// Log signup
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($user->id, 'user', NULL, 'signup');
			}
            
			// Start session
			foreach ($user as $key => $value) {
				$user_array[$key] = $value;
			}
			$_SESSION['user'] = $user_array;
            
			// Check invites are enabled and the code is valid
			if ($this->config->invites['enabled'] == TRUE && Invite::check_code_valid($_POST['code'], $_POST['email']) == TRUE) {
				
				// Get invites
				$invites = Invite::list_by_code($_POST['code']);
				
				if (is_array($invites)) {
					foreach ($invites as $invite) {
						
						// Update invites
						Invite::update($invite->id);
						
						// Log invite update
						if (isset($this->plugins->log)) {
							$this->plugins->log->add($_SESSION['user']['id'], 'invite', $invite->id, 'accept');
						}
						
						// Update points (but only if inviting user is not an admin)
						if (isset($this->plugins->points) && in_array($invite->user_id, $this->config->admin_users) != TRUE) {
							
							// Update points
							$this->plugins->points->update($invite->user_id, $this->plugins->points['per_invite_accepted']);
							
							// Log points update
							if (isset($this->plugins->log)) {
								$this->plugins->log->add($invite->user_id, 'points', NULL, $this->plugins->points['per_invite_accepted'], 'invite_accepted = '.$invite->id);
							}
							
						}
						
					}
					// end foreach
				}
				// end if is_array
            
			}
            
			// Log login
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'login');
			}
            
			// If redirect_to is set then redirect
			if ($_GET['redirect_to']) {
				header('Location: '.$_GET['redirect_to']);
				exit();
			}
            
			// Set welcome message
			$this->message = urlencode('Welcome to '.$this->config->name.'!');
            
			// Go forth!
			if (SITE_IDENTIFIER == 'live') {
				header('Location: '.$this->config->url.$this->config->default_controller.'/?message='.$this->message);
			} else {
				header('Location: '.$this->config->dev_url.$this->config->default_controller.'/?message='.$this->message);
			}
            
			exit();
			
		} else {
			// There was an error
			
			// Propagate get vars to be picked up by the form
			$_GET['email']		= $_POST['email'];
			$_GET['username']	= $_POST['username'];
			$_GET['code']		= $_POST['code'];
			
			// Show error message
			$this->message = $error;
			$this->title = 'Signup';
			
			// Show signup form
			$this->loadLayout('users/add');
			
		}
		
	}
	
	// Helper function: checks email is valid and available, returns TRUE or error message
	private function check_email($email) {
		
		if ($email == '') {
			$return .= 'Email cannot be left blank.<br />';
		}

		if (User::check_contains_spaces($email) == TRUE) {
			$return .= 'Email cannot contain spaces.<br />';
		}

		if (User::check_contains_at($email) != TRUE) {
			$return .= 'Email must contain an @ symbol.<br />';
		}

		if (User::check_email_available($email) != TRUE) {
			$return .= 'Email already in the system!<br />';
		}
		
		if (empty($return)) {
			$return = TRUE;
		}
		
		return $return;
		
	}
	
	// Helper function: checks username is valid and available, returns TRUE or error message
	private function check_username($username) {
		
		if ($username == '') {
			$return .= 'Username cannot be left blank.<br />';
		}
        
		if (User::check_alphanumeric($username) != TRUE) {
			$return .= 'Username must only contain letters and numbers.<br />';
		}
        
		if (User::check_username_available($username) != TRUE) {
			$return .= 'Username not available.<br />';
		}
		
		if (empty($return)) {
			$return = TRUE;
		}
		
		return $return;
		
	}
	
}

?>
