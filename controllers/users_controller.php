<?php

class UsersController extends Application {

	protected $requireLoggedOut = array('add');
	protected $requireLoggedIn = array('show', 'update', 'reset', 'confirm');
	
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
			
			$this->code = $code;
			$this->loadView('users/add');
			
		}
		
	}
	
	// Show a user / user page
	function show($id) {
		
		$this->user = User::get_by_id($id);
		
		// id failed so try username (used by routes)
		if ($this->user == NULL) {
			$this->user = User::get_by_username($id);
		}
		
		// page zero so overwrite to 1
		if ($this->uri['params']['page'] == 0) {
			$this->uri['params']['page'] = 1;
		}
		
		// items per page, change this to test pagination
		$limit = 10;
		
		if (isset($this->uri['params']['page'])) {
			$offset = ($this->uri['params']['page'] - 1) * $limit;
		} else {
			$offset = 0;
		}
		
		$this->items = $this->user->items($limit, $offset);
		
		$this->title = $this->user->username;		
		
		if ($this->json) {
			$this->render_json($this->user);
		} else {
			$this->loadView('users/show');
		}
	
	}
	
	// Update user: change passsword, update profile
	function update($page) {
		
		$user = User::get_by_id($_SESSION['user_id']);
		
		if (!isset($page)) {
			
			$page = 'profile';
			
		} elseif ($page == 'password') {
			
			if ($_POST['old_password'] != '' && $_POST['new_password1'] != '' && $_POST['new_password2'] != '') {
				$this->update_password($this->config->encryption_salt);
			}
			
		} elseif ($page == 'profile') {
			
			if ($_POST['full_name'] != '' || $_POST['bio'] != '' || $_POST['url'] != '') {
				$this->update_profile();
			}
			
		}
		
		$this->title = 'Settings';
		$this->page = $page;
		$this->user = User::get_by_id($_SESSION['user_id']);
		
		$this->loadView('users/update');
		
	}
	
	// Password reset
	function reset($code) {
		
		if (isset($_SESSION['user_id'])) {
			
			$this->title = 'Page not found';
			$this->loadView();
			exit;
			
		}
		
		if (isset($code)) {
			// Process reset
			
			// If two passwords submitted then check, otherwise show form
			if ($_POST['password1'] != '' && $_POST['password2'] != '') {
				
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
					
					// Get user object
					$user = User::get_by_id($user_id);
					
					// Do update
					$user->update_password($_POST['password1'], $this->config->encryption_salt);
					
					$user->authenticate($_POST['password1'], $this->config->encryption_salt);
					
					// Set welcome message
					Application::flash('success', 'Password updated! Welcome back to '.$this->config->name.'!');
					
					// If redirect_to is set then redirect
					if (isset($this->uri['params']['redirect_to'])) {
						header('Location: '.$this->uri['params']['redirect_to']);
						exit();
					}
					
					// Go forth!
					if (SITE_IDENTIFIER == 'live') {
						header('Location: '.$this->config->url.$this->config->default_controller);
					} else {
						header('Location: '.$this->config->dev_url.$this->config->default_controller);
					}
					
					exit();
					
				} else {
					// Show error message
					
					if (User::check_password_reset_code($code) != FALSE) {
						Application::flash('error', $error);
						$this->loadView('users/reset');
					} else {
						$this->loadView();
					}
					
				}
				
			} else {
				// Code present so show password reset form
				
				if (User::check_password_reset_code($code) == TRUE) {
					// Invite code valid
					
					$this->code = $code;
					$this->loadView('users/reset');

				} else {
					
					$this->title = 'Page not found';
					$this->loadView();
					exit;
					
				}
				
			}
			
		} elseif (!isset($_SESSION['user_id'])) {
			// No code in URL so show new reset form
			
			if ($_POST['email'] != '') {
				// Email submitted so send password reset email
				
				$user = User::get_by_email($_POST['email']);
				
				// Check is a user
				if ($user != NULL) {
					
					// Generate code
					$code = $user->generate_password_reset_code();
					
					$to = $_POST['email'];
					$link = substr($this->config->url, 0, -1).$this->url_for('users', 'reset', $code);
					$headers = "From: {$this->config->name} <robot@blah.com>\r\nContent-type: text/html\r\n";
					
					// Load subject and body from template
					include "themes/{$this->config->theme}/emails/password_reset.php";
					
					// Email user
					if ($this->config->send_emails == TRUE) {
						mail($to, $subject, $body, $headers);
					}
					
				}
				
				Application::flash('info', 'Check your email for instructions about how to reset your password!');
				
			}
				
			$this->loadView('users/reset');
			
		} else {
			
			$this->title = 'Page not found';
			$this->loadView();
			exit;
			
		}
		
	}
	
	// Confirm email address
	function confirm($email) {
		
		
		
	}
		
	// Helper function: update password
	private function update_password($salt) {
		
		if (md5($_POST['old_password'].$salt) == $user->password) {
			// Check old passwords match
			
			if ($_POST['new_password1'] == $_POST['new_password2']) {
				// New passwords match
				
				$user = User::get_by_id($_SESSION['user_id']);
				
				// Call update_password in user model
				$user->update_password($_POST['new_password1'], $salt);
				
				// Update session
				$user->password = md5($_POST['new_password1'].$salt);
				
				// Log password update
				if (isset($this->plugins->log)) {
					$this->plugins->log->add($_SESSION['user_id'], 'user', NULL, 'change_password');
				}
				
				Application::flash('success', 'Password updated!');
				
			} else {
				// New passwords don't match
				
				Application::flash('error', 'There was a problem, please try again.');
				
			}
			
		} else {
			// Old passwords don't match
			
			Application::flash('error', 'There was a problem, please try again.');
			
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
			$error .= 'URL cannot contain spaces.';
		}
        
		// End URL validation
        
		if ($error == '') {
			
			$user = User::get_by_id($_SESSION['user_id']);
			
			// Call update_profile in user model
			$user->update_profile($_POST['full_name'], $_POST['bio'], $_POST['url']);
        	
			// Set success message
			Application::flash('success', 'Profile information updated!');
        	
		} else {
        	
			Application::flash('error', $error);
        	
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
            	
				$admin = User::get_by_id($this->config->admin_users[0]);
				
				$to			= "{$_POST['username']} <{$_POST['email']}>";
				$headers	= "From: {$admin->username} <{$admin->email}>\r\nBcc: {$admin->email}\r\nContent-type: text/html\r\n";
            	
				// Load subject and body from template
				include "themes/{$this->config->theme}/emails/signup.php";
            	
				// Email user
				mail($to, $subject, $body, $headers);
            	
			}
            
			// Log signup
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($user->id, 'user', NULL, 'signup');
			}
            
			// Start session
			$_SESSION['user_id'] = $user->id;
            
			// Check invites are enabled
			if ($this->config->invites['enabled'] == TRUE) {
				
				// Get invites
				$invites = Invite::list_by_code($_POST['code']);
				
				if (is_array($invites)) {
					foreach ($invites as $invite) {
						
						// Update invites
						$invite->update();
						
						// Log invite update
						if (isset($this->plugins->log)) {
							$this->plugins->log->add($_SESSION['user_id'], 'invite', $invite->id, 'accept');
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
				$this->plugins->log->add($_SESSION['user_id'], 'user', NULL, 'login');
			}
            
			// If redirect_to is set then redirect
			if ($this->uri['params']['redirect_to']) {
				header('Location: '.$this->uri['params']['redirect_to']);
				exit();
			}
            
			// Set welcome message
			Application::flash('success', 'Welcome to '.$this->config->name.'!');
            
			// Go forth!
			if (SITE_IDENTIFIER == 'live') {
				header('Location: '.$this->config->url.$this->config->default_controller);
			} else {
				header('Location: '.$this->config->dev_url.$this->config->default_controller);
			}
            
			exit();
			
		} else {
			// There was an error
			
			// Propagate get vars to be picked up by the form
			$this->uri['params']['email']		= $_POST['email'];
			$this->uri['params']['username']	= $_POST['username'];
			$this->code			= $_POST['code'];
			
			// Show error message
			Application::flash('error', $error);
			$this->title = 'Signup';
			
			// Show signup form
			$this->loadView('users/add');
			
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
			Application::flash('success', 'Thanks for signing up!<br />We will be in touch soon...');
            
			// Go forth!
			if (SITE_IDENTIFIER == 'live') {
				header('Location: '.$this->config->url);
			} else {
				header('Location: '.$this->config->dev_url);
			}
            
			exit();
            
		} else {
			// There was an error
			
			// Propagate get vars to be picked up by the form
			$this->uri['params']['email']		= $_POST['email'];
			$this->uri['params']['username']	= $_POST['username'];
			$this->code			= $_POST['code'];
			
			// Show error message
			Application::flash('error', $error);
			$this->title = 'Beta signup';
			
			// Show signup form
			$this->loadView('users/add');
			
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
				
				$admin = User::get_by_id($this->config->admin_users[0]);
				
				$to = "{$_POST['username']} <{$_POST['email']}>";
				$headers	= "From: {$admin->username} <{$admin->email}>\r\nBcc: {$admin->email}\r\nContent-type: text/html\r\n";
				
				// Load subject and body from template
				include "themes/{$this->config->theme}/emails/signup.php";
				
				// Email user
				mail($to, $subject, $body, $headers);
				
			}
            
			// Log signup
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($user->id, 'user', NULL, 'signup');
			}
            
			// Start session
			$_SESSION['user_id'] = $user->id;
            
			// Check invites are enabled and the code is valid
			if ($this->config->invites['enabled'] == TRUE && Invite::check_code_valid($_POST['code'], $_POST['email']) == TRUE) {
				
				// Get invites
				$invites = Invite::list_by_code($_POST['code']);
				
				if (is_array($invites)) {
					foreach ($invites as $invite) {
						
						// Update invites
						$invite->update();
						
						// Log invite update
						if (isset($this->plugins->log)) {
							$this->plugins->log->add($_SESSION['user_id'], 'invite', $invite->id, 'accept');
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
				$this->plugins->log->add($_SESSION['user_id'], 'user', NULL, 'login');
			}
            
			// If redirect_to is set then redirect
			if ($this->uri['params']['redirect_to']) {
				header('Location: '.$this->uri['params']['redirect_to']);
				exit();
			}
            
			// Set welcome message
			Application::flash('success', 'Welcome to '.$this->config->name.'!');
            
			// Go forth!
			if (SITE_IDENTIFIER == 'live') {
				header('Location: '.$this->config->url.$this->config->default_controller);
			} else {
				header('Location: '.$this->config->dev_url.$this->config->default_controller);
			}
            
			exit();
			
		} else {
			// There was an error
			
			// Propagate get vars to be picked up by the form
			$this->uri['params']['email']		= $_POST['email'];
			$this->uri['params']['username']	= $_POST['username'];
			$this->code			= $_POST['code'];
			
			// Show error message
			Application::flash('error', $error);
			$this->title = 'Signup';
			
			// Show signup form
			$this->loadView('users/add');
			
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
