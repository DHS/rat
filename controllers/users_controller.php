<?php

class UsersController extends Application {

	protected $requireLoggedOut = array('add', 'reset');
	protected $requireLoggedIn = array('update', 'confirm');

	// Add a user / signup
	function add($code = NULL) {

		if (isset($_POST['email'])) {
			//User is trying to signup

      // User trying to sign up but app not configured, error out
      if (Admin::count_users() == 0) {

        Application::flash('error', $this->config->name . ' is not yet configured properly.
          <br />Please contact the creator of this app.');
        $this->loadView('items/index');
        exit();

      }

			if ($code != NULL) {
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

			$this->loadView('users/add', array('code' => $code));

		}

	}

	// Show a user / user page
	function show($id) {

		$user = User::get_by_id($id);

		// id failed so try username (used by routes)
		if ($user == NULL) {
			$user = User::get_by_username($id);
		}

		// Page zero so overwrite to 1
		if ( ! isset($this->uri['params']['page'])) {
			$this->uri['params']['page'] = 1;
		}

		// items per page, change this to test pagination
		$limit = 10;

		if ($this->uri['params']['page'] == 1) {
			$offset = 0;
		} else {
			$offset = ($this->uri['params']['page'] - 1) * $limit;
		}

		$items = $user->items($limit, $offset);

		foreach ($items as $item) {
			$item->content = process_content($item->content);
			foreach ($item->comments as $comment) {
				$comment->content = process_content($comment->content);
			}
			foreach ($item->likes as $like) {
			  if (isset($_SESSION['user_id']) && $like->user_id == $_SESSION['user_id']) {
			    $item->i_like = true;
			  } else {
			    $item->i_like = false;
			  }
			}
		}

		if ($this->config->friends['enabled'] == TRUE) {
			$friends = $user->friend_check($_SESSION['user_id']);
		}

    if (isset($user->username)) {
      $this->title = $user->username;
    }

		if ($this->json) {
			$this->render_json($this->user);
		} else {
		  $vars = array('user' => $user, 'items' => $items);
		  if (isset($friends)) {
		    $vars['friends'] = $friends;
		  }
			$this->loadView('users/show', $vars);
		}

	}

	// Update user: change passsword, update profile
	function update($page = 'profile') {

		$user = User::get_by_id($_SESSION['user_id']);

		if ($page == 'password') {

			if (isset($_POST['old_password']) && $_POST['old_password'] != '' && isset($_POST['new_password1']) && $_POST['new_password1'] != '' && isset($_POST['new_password2']) && $_POST['new_password2'] != '') {
				$this->update_password($this->config->encryption_salt);
			}

		} elseif ($page == 'profile') {

			if (isset($_POST['full_name']) || isset($_POST['bio']) || isset($_POST['url'])) {
				$this->update_profile();
				$user->full_name = $_POST['full_name'];
				$user->bio = $_POST['bio'];
				$user->url = $_POST['url'];
			}

		}

		$this->loadView('users/update', array('page' => $page, 'user' => $user));

	}

	// Password reset
	function reset($code) {

		if (isset($code)) {
			// Process reset

			// If two passwords submitted then check, otherwise show form
			if ($_POST['password1'] != '' && $_POST['password2'] != '') {

				if (User::check_password_reset_code($code) == FALSE) {
					exit();
				}

				$error = '';

				// Check password
    		$password_check = $this->check_password($_POST['password1'], $_POST['password2']);
    		if ($password_check !== TRUE) {
    			$error .= $password_check;
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
					Application::flash('success', 'Password updated! Welcome back to ' . $this->config->name . '!');

					// If redirect_to is set then redirect
					if (isset($this->uri['params']['redirect_to'])) {
						header('Location: ' . $this->uri['params']['redirect_to']);
						exit();
					}

					// Go forth!
					header('Location: ' . $this->config->url);

					exit();

				} else {
					// Show error message

					if (User::check_password_reset_code($code) == TRUE) {

						$valid_code = TRUE;

						Application::flash('error', $error);

						$this->loadView('users/reset', array('valid_code' => $valid_code, 'code' => $code));

					} else {
						$this->loadView();
					}

				}

			} else {
				// Code present so show password reset form

				if (User::check_password_reset_code($code) == TRUE) {
					// Invite code valid

					$valid_code = TRUE;

					$this->loadView('users/reset', array('valid_code' => $valid_code, 'code' => $code));

				} else {

					$this->title = 'Page not found';
					$this->loadView();
					exit;

				}

			}

		} else {
			// No code in URL so show new reset form

			if ($_POST['email'] != '') {
				// Email submitted so send password reset email

				$user = User::get_by_email($_POST['email']);

				// Check is a user
				if ($user != NULL) {

					// Generate code
					$code = $user->generate_password_reset_code();

					$to       = array('email' => $_POST['email']);
					$link		  = substr($this->config->url, 0, -1).$this->url_for('users', 'reset', $code);
					$subject  = '[' . $this->config->name . '] Password reset';
					$body     = $this->twig_string->render(file_get_contents("themes/{$this->config->theme}/emails/password_reset.html"), array('link' => $link, 'user' => $user, 'app' => array('config' => $this->config)));

					// Email user
					$this->email->send_email($to, $subject, $body);

				}

				Application::flash('info', 'Check your email for instructions about how to reset your password!');

			}

			$this->loadView('users/reset', array('valid_code' => $valid_code, 'code' => $code));

		}

	}

	// Confirm email address
	function confirm($email) {



	}

	// Helper function: update password
	private function update_password($salt) {

		$user = User::get_by_id($_SESSION['user_id']);

		if (md5($_POST['old_password'] . $salt) == $user->password) {
			// Check old passwords match

			if ($_POST['new_password1'] == $_POST['new_password2']) {
				// New passwords match

				// Call update_password in user model
				$user->update_password($_POST['new_password1'], $salt);

				// Update session
				$user->password = md5($_POST['new_password1'] . $salt);

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
			$_POST['url'] = 'http://' . $_POST['url'];
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

		$error = '';

		// Check invite code (only really matters if app is in beta)

		if ($this->config->beta == TRUE) {

			if (Invite::check_code_valid($_POST['code'], $_POST['email']) != TRUE) {
				$error .= 'Invalid invite code.<br />';
			}

		}

		// Check email
		$_POST['email'] = trim($_POST['email']);
		$email_check = $this->check_email($_POST['email'], FALSE);
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

			$admin = User::get_by_id($this->config->admin_users[0]);

			$to       = array('name' => $_POST['username'], 'email' => $_POST['email']);
			$subject  = '[' . $this->config->name . '] Your ' . $this->config->name . ' invite is here!';
			$body     = $this->twig_string->render(file_get_contents("themes/{$this->config->theme}/emails/signup.html"), array('username' => $_POST['username'], 'app' => array('config' => $this->config)));

			$this->email->send_email($to, $subject, $body);

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
								$this->plugins->log->add($invite->user_id, 'points', NULL, $this->plugins->points['per_invite_accepted'], 'invite_accepted = ' . $invite->id);
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
				header('Location: ' . $this->uri['params']['redirect_to']);
				exit();
			}

			// Set welcome message
			Application::flash('success', 'Welcome to ' . $this->config->name . '!');

			// Go forth!
			header('Location: ' . $this->config->url);

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
			$this->loadView('users/add', array('code' => $_POST['code']));

		}

	}

	// Helper function: beta signup
	private function signup_beta() {

		$error = '';

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
			header('Location: ' . $this->config->url);

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

		$error = '';

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
		$password_check = $this->check_password($_POST['password1'], $_POST['password2']);
		if ($password_check !== TRUE) {
			$error .= $password_check;
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

				$to = array('name' => $_POST['username'], 'email' => $_POST['email']);
				$subject = '[' . $this->config->name . '] Welcome to ' . $this->config->name . '!';
				$body = $this->twig_string->render(file_get_contents("themes/{$this->config->theme}/emails/signup.html"), array('username' => $_POST['username'], 'app' => array('config' => $this->config)));

				// Email user
				$this->email->send_email($to, $subject, $body, TRUE);

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
								$this->plugins->log->add($invite->user_id, 'points', NULL, $this->plugins->points['per_invite_accepted'], 'invite_accepted = ' . $invite->id);
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
				header('Location: ' . $this->uri['params']['redirect_to']);
				exit();
			}

			// Set welcome message
			Application::flash('success', 'Welcome to ' . $this->config->name . '!');

			// Go forth!
			header('Location: ' . $this->config->url);

			exit();

		} else {
			// There was an error

			// Propagate get vars to be picked up by the form
			$this->uri['params']['email'] = $_POST['email'];
			$this->uri['params']['username'] = $_POST['username'];
			if (isset($_POST['code'])) {
			  $this->code = $_POST['code'];
			}

			// Show error message
			Application::flash('error', $error);
			$this->title = 'Signup';

			// Show signup form
			$this->loadView('users/add');

		}

	}

	// Helper function: checks email is valid and available, returns TRUE or error message
	private function check_email($email, $new = TRUE) {

    $return = '';

		if ($email == '') {
			$return .= 'Email cannot be left blank.<br />';
		}

		if (User::check_contains_spaces($email) == TRUE) {
			$return .= 'Email cannot contain spaces.<br />';
		}

		if (User::check_contains_at($email) != TRUE) {
			$return .= 'Email must contain an @ symbol.<br />';
		}

		if (User::check_email_available($email) != TRUE && $new == TRUE) {
			$return .= 'An account with that email address already exists in the system. ' . $this->get_link_to('Click here', 'sessions', 'add') . ' to login.<br />';
		}

    return strlen($return) > 0 ? $return : TRUE;

	}

	// Helper function: checks username is valid and available, returns TRUE or error message
	private function check_username($username) {

    $return = '';

		if ($username == '') {
			$return .= 'Username cannot be left blank.<br />';
		}

		if (User::check_alphanumeric($username) != TRUE) {
			$return .= 'Username must only contain letters and numbers.<br />';
		}

		if (User::check_username_available($username) != TRUE) {
			$return .= 'Username not available.<br />';
		}

    return strlen($return) > 0 ? $return : TRUE;

	}

	// Helper function: checks passwords match and are good, returns TRUE or error message
	private function check_password($password1, $password2) {

    $return = '';

    // Easily guessable passwords
    $easy_passwords = array(
      '123', '1234', '12345', '123456', '1234567', '12345678',
      'password', 'qwerty', 'letmein', 'test', 'blah', 'hello'
    );

	  if ($password1 == '' || $password2 == '') {
			$return .= 'Please enter your password twice.<br />';
		}

		if ($password1 != $password2) {
			$return .= 'Passwords do not match.<br />';
		}

    if (in_array($password1, $easy_passwords)) {
			$return .= 'Password must not be easy to guess.<br />';
		}

		if (strlen($password1) < 3) {
			$return .= 'Password must be more than two characters long.<br />';
		}

    return strlen($return) > 0 ? $return : TRUE;

	}

}
