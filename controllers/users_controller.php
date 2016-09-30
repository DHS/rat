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

      $this->loadView('users/add', array('email' => $_GET['email'], 'code' => $code));

    }

  }

  // Show a user / user page
  function show($id) {

    $user = User::get_by_id($id);

    // id failed so try username (used by routes)
    if ($user == null) {
      $user = User::get_by_username($id);
    }

    // username failed so error out
    if ($user == null) {
      throw new RoutingException($this->uri, "User not found");
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

    if ($this->config->friends->enabled == TRUE) {
      $friends = $user->friend_check($_SESSION['user_id']);
    }

    if ($this->json) {

      $this->render_json($user);

    } else {

      $vars = array('user' => $user, 'items' => $items);
      if (isset($friends)) {
        $vars['friends'] = $friends;
      }

      if (isset($user->username)) {
        $vars['title'] = $user->username;
      }

      $this->loadView('users/show', $vars);

    }

  }

  // Update user: change passsword, update profile
  function update($page = 'profile') {

    $this->user = User::get_by_id($_SESSION['user_id']);

    if ($page == 'password') {

      if (isset($_POST['old_password']) && $_POST['old_password'] != '' && isset($_POST['new_password1']) && $_POST['new_password1'] != '' && isset($_POST['new_password2']) && $_POST['new_password2'] != '') {
        $this->update_password();
      }

    } elseif ($page == 'profile') {

      if (isset($_POST['full_name']) || isset($_POST['bio']) || isset($_POST['url'])) {
        $this->update_profile();
      }

    } elseif ($page == 'emails') {

      if (isset($_POST['submit'])) {
        $this->update_email_notifications();
      }

    }

    $this->loadView('users/update', array('page' => $page, 'user' => $this->user));

  }

  // Password reset
  function reset($code = null) {

    if ($code != null) {
      // Process reset

      // If two passwords submitted then check, otherwise show form
      if (isset($_POST['password1']) && $_POST['password1'] != '' && isset($_POST['password2']) && $_POST['password2'] != '') {

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

          // Get redirected
          if (isset($this->uri['params']['redirect_to'])) {
            $redirect_url = $this->uri['params']['redirect_to'];
          } else {
            $redirect_url = $this->config->url;
          }

          // Go forth
          header('Location: ' . $redirect_url);
          exit();

        } else {
          // Show error message

          if (User::check_password_reset_code($code) == TRUE) {

            Application::flash('error', $error);

            $this->loadView('users/reset', array('valid_code' => TRUE, 'code' => $code));

          } else {

            $this->loadView();

          }

        }

      } else {
        // Code present so show password reset form

        if (User::check_password_reset_code($code) == TRUE) {
          // Invite code valid

          $this->loadView('users/reset', array('valid_code' => TRUE, 'code' => $code));

        } else {

          throw new RoutingException($uri, "Page not found");

        }

      }

    } else {
      // No code in URL so show new reset form

      if (isset($_POST['email'])) {
        // Email submitted so send password reset email

        $user = User::get_by_email($_POST['email']);

        // Check is a user
        if ($user != NULL) {

          // Generate code
          $code = $user->generate_password_reset_code();

          $to       = array('email' => $_POST['email']);
          $link      = substr($this->config->url, 0, -1).$this->url_for('users', 'reset', $code);
          $subject  = '[' . $this->config->name . '] Password reset';
          $body     = $this->twig_string->render(file_get_contents("themes/{$this->config->theme}/emails/password_reset.html"), array('user' => $user, 'link' => $link, 'app' => $this));

          // Email user
          $this->email->send_email($to, $subject, $body);

        }

        Application::flash('info', 'Check your email for instructions about how to reset your password!');

      }

      $this->loadView('users/reset', array('valid_code' => FALSE, 'code' => $code));

    }

  }

  // Confirm email address
  function confirm($email) {



  }

  // Helper function: update password
  private function update_password() {

    $this->user = User::get_by_id($_SESSION['user_id']);

    $error = '';

    if (md5($_POST['old_password'] . $this->config->encryption_salt) != $this->user->password) {
      // Old passwords don't match

      $error .= 'Incorrect existing password.<br />';

    }

    // Check password
    $password_check = $this->check_password($_POST['new_password1'], $_POST['new_password2']);
    if ($password_check !== TRUE) {
      $error .= $password_check;
    }

    if ($error == '') {

      // Call update_password in user model
      $this->user->update_password($_POST['new_password1'], $this->config->encryption_salt);

      // Update session
      $this->user->password = md5($_POST['new_password1'] . $this->config->encryption_salt);

      // Log password update
      if (isset($this->plugins->log)) {
        $this->plugins->log->add($_SESSION['user_id'], 'user', NULL, 'change_password');
      }

      Application::flash('success', 'Password updated!');

    } else {

      // Show error message
      Application::flash('error', $error);

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

      // Call update_profile in user model
      $this->user->update_profile($_POST['full_name'], $_POST['bio'], $_POST['url']);

      $this->user->full_name = $_POST['full_name'];
      $this->user->bio = $_POST['bio'];
      $this->user->url = $_POST['url'];

      // Set success message
      Application::flash('success', 'Profile information updated!');

    } else {

      Application::flash('error', $error);

    }

  }

  //  Helper function: update profile
  private function update_email_notifications() {

    // Unset submit var
    unset($_POST['submit']);

    // Loop through remainig post vars fetching new email settings
    foreach ($_POST as $key => $value) {

      if ($value == 'on') {
        $emails[$key] = 1;
      }

    }

    // Check for existing settings in order to update
    // with negative values
    foreach ($this->user->email_notifications as $key => $value) {

      if ( ! isset($_POST[$key])) {
        $emails[$key] = 0;
      }

    }

    // Call update_emails in user model
    $this->user->update_email_notifications($emails);

    // Update the current user
    $this->user->email_notifications = $emails;

    // Set success message
    Application::flash('success', 'Email settings updated!');

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
    $email_check = $this->check_email($_POST['email'], TRUE);
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

      $admin = User::get_by_id($this->config->admin_users[0]);

      $to       = array('name' => $_POST['username'], 'email' => $_POST['email']);
      $subject  = '[' . $this->config->name . '] Your ' . $this->config->name . ' invite is here!';
      $body     = $this->twig_string->render(file_get_contents("themes/{$this->config->theme}/emails/signup.html"), array('username' => $_POST['username'], 'app' => $this));

      $this->email->send_email($to, $subject, $body);

      // Log signup
      if (isset($this->plugins->log)) {
        $this->plugins->log->add($user->id, 'user', NULL, 'signup');
      }

      // Start session
      $_SESSION['user_id'] = $user->id;

      // Check invites are enabled
      if ($this->config->invites->enabled == TRUE) {

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
      $this->uri['params']['email']     = $_POST['email'];
      $this->uri['params']['username']  = $_POST['username'];
      $this->code                       = $_POST['code'];

      // Show error message
      Application::flash('error', $error);

      // Show signup form
      $this->loadView('users/add', array('title' => 'Signup', 'code' => $_POST['code']));

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

      // Admin alert email
      if ($this->config->send_emails && $this->config->signup_email_notifications == TRUE) {

        $admin = User::get_by_id($this->config->admin_users[0]);

        $to       = array('name' => $admin->username, 'email' => $admin->email);
        $subject  = '[' . $this->config->name . '] New signup on ' . $this->config->name . '!';
        $link     = substr($this->config->url, 0, -1) . $this->url_for('admin', 'signups');
        $body     = $this->twig_string->render(file_get_contents("themes/{$this->config->theme}/emails/admin_signup_notification.html"), array('link' => $link, 'app' => $this));

        // Email user
        $this->email->send_email($to, $subject, $body);

      }

      // Set thank you & tweet this message
      Application::flash('success', 'Thanks for signing up!<br />We will be in touch soon...');

      // Go forth!
      header('Location: ' . $this->config->url);

      exit();

    } else {
      // There was an error

      // Propagate get vars to be picked up by the form
      $this->uri['params']['email'] = $_POST['email'];
      $this->uri['params']['username'] = $_POST['username'];
      $this->code = $_POST['code'];

      // Show error message
      Application::flash('error', $error);

      // Show signup form
      $this->loadView('users/add', array('title' => 'Beta signup'));

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

        $to       = array('name' => $_POST['username'], 'email' => $_POST['email']);
        $subject  = '[' . $this->config->name . '] Welcome to ' . $this->config->name . '!';
        $body     = $this->twig_string->render(file_get_contents("themes/{$this->config->theme}/emails/signup.html"), array('username' => $_POST['username'], 'app' => $this));

        // Email user
        $this->email->send_email($to, $subject, $body);

      }

      // Log signup
      if (isset($this->plugins->log)) {
        $this->plugins->log->add($user->id, 'user', NULL, 'signup');
      }

      // Admin alert email
      if ($this->config->send_emails && $this->config->signup_email_notifications == TRUE) {

        $admin = User::get_by_id($this->config->admin_users[0]);

        $to       = array('name' => $admin->username, 'email' => $admin->email);
        $subject  = '[' . $this->config->name . '] New signup on ' . $this->config->name . '!';
        $link     = substr($this->config->url, 0, -1) . $this->url_for('users', 'show', $user->id);
        $body     = $this->twig_string->render(file_get_contents("themes/{$this->config->theme}/emails/admin_signup_notification.html"), array('link' => $link, 'app' => $this));

        // Email user
        $this->email->send_email($to, $subject, $body);

      }

      // Start session
      $_SESSION['user_id'] = $user->id;

      // Check invites are enabled and the code is valid
      if ($this->config->invites->enabled == TRUE && Invite::check_code_valid($_POST['code'], $_POST['email']) == TRUE) {

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
      $vars = array('title' => 'Signup');

      if (isset($_POST['email']) && $_POST['email'] != '') {
        $vars['email'] = $_POST['email'];
      }

      if (isset($_POST['username']) && $_POST['username'] != '') {
        $vars['username'] = $_POST['username'];
      }

      if (isset($_POST['code']) && $_POST['code'] != '') {
        $vars['code'] = $_POST['code'];
      }

      // Show error message
      Application::flash('error', $error);

      // Show signup form
      $this->loadView('users/add', $vars);

    }

  }

  // Helper function: checks email is valid and available, returns TRUE or error message
  private function check_email($email, $existingUser = FALSE) {

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

    if (User::check_email_available($email) != TRUE) {
      if ($existingUser == FALSE) {
        $return .= 'An account with that email address already exists in the system. ' . $this->get_link_to('Click here', 'sessions', 'add') . ' to login.<br />';
      }
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
      'password', '123', '1234', '12345', '123456', '1234567', '12345678', 'abc123',
      'qwerty', 'letmein', 'test', 'blah', 'hello', 'jesus', 'iloveyou', 'monkey', 'princess'
    );

    if ($password1 == '' || $password2 == '') {
      $return .= 'Please enter your password twice.<br />';
    }

    if ($password1 != $password2) {
      $return .= 'New passwords do not match.<br />';
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
