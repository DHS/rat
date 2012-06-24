<?php

class AdminController extends Application {

  protected $requireAdmin = array('index', 'signups', 'users', 'history', 'invite', 'grant_invites');

  // Show admin dashboard
  function index() {

    $users = Admin::list_users();
    $users_beta = Admin::list_users_beta();

    $this->loadView('admin/index', array('users' => $users, 'users_beta' => $users_beta), 'admin');

  }

  // Show app config
  function config() {

    $conf = get_object_vars($this->config);

    if ($_POST) {

      // Pull post vars into $conf array

      foreach ($_POST as $key => $value) {
        $conf[$key] = $value;
      }

      //// Overwrite checkbox fields
      //$checkboxes = array('beta', 'private', 'items\'][\'titles\'][\'enabled', 'items["content"]["enabled"]', 'items["uploads"]["enabled"]', 'items["comments"]["enabled"]', 'items["likes"]["enabled"]');
      //
      //foreach ($checkboxes as $key => $checkbox) {
      //  if ($_POST[$checkbox] == 'on') {
      //    $conf[$checkbox] = 'TRUE';
      //  } else {
      //    $conf[$checkbox] = 'FALSE';
      //  }
      //}

      if (isset($_POST['beta']) && $_POST['beta'] == 'on') {
        $conf['beta'] = 'TRUE';
      } else {
        $conf['beta'] = 'FALSE';
      }

      if (isset($_POST['private']) && $_POST['private'] == 'on') {
        $conf['private'] = 'TRUE';
      } else {
        $conf['private'] = 'FALSE';
      }

      if (isset($_POST['items']['titles']['enabled']) && $_POST['items']['titles']['enabled'] == 'on') {
        $conf['items']['titles']['enabled'] = 'TRUE';
      } else {
        $conf['items']['titles']['enabled'] = 'FALSE';
      }

      if (isset($_POST['items']['content']['enabled']) && $_POST['items']['content']['enabled'] == 'on') {
        $conf['items']['content']['enabled'] = 'TRUE';
      } else {
        $conf['items']['content']['enabled'] = 'FALSE';
      }

      if (isset($_POST['items']['uploads']['enabled']) && $_POST['items']['uploads']['enabled'] == 'on') {
        $conf['items']['uploads']['enabled'] = 'TRUE';
      } else {
        $conf['items']['uploads']['enabled'] = 'FALSE';
      }

      if (isset($_POST['items']['comments']['enabled']) && $_POST['items']['comments']['enabled'] == 'on') {
        $conf['items']['comments']['enabled'] = 'TRUE';
      } else {
        $conf['items']['comments']['enabled'] = 'FALSE';
      }

      if (isset($_POST['items']['likes']['enabled']) && $_POST['items']['likes']['enabled'] == 'on') {
        $conf['items']['likes']['enabled'] = 'TRUE';
      } else {
        $conf['items']['likes']['enabled'] = 'FALSE';
      }

      if (isset($_POST['invites']['enabled']) && $_POST['invites']['enabled'] == 'on') {
        $conf['invites']['enabled'] = 'TRUE';
      } else {
        $conf['invites']['enabled'] = 'FALSE';
      }

      if (isset($_POST['friends']['enabled']) && $_POST['friends']['enabled'] == 'on') {
        $conf['friends']['enabled'] = 'TRUE';
      } else {
        $conf['friends']['enabled'] = 'FALSE';
      }

      if (isset($_POST['friends']['asymmetric']) && $_POST['friends']['asymmetric'] == 'on') {
        $conf['friends']['asymmetric'] = 'TRUE';
      } else {
        $conf['friends']['asymmetric'] = 'FALSE';
      }

      // Update config
      $this->writeConfig('application', $conf);

      // Set flash message
      Application::flash('success', 'App config updated!');

      // Force redirect to reload app with new config
      header('Location: /admin/config');
      exit();

    }

    $this->loadView('admin/config', null, 'admin');

  }

  // Show list of beta signups
  function signups() {

    $users = Admin::list_users_beta();

    $this->loadView('admin/signups', array('users' => $users), 'admin');

  }

  // Show list of users
  function users() {

    $users = Admin::list_users();

    $this->loadView('admin/users', array('users' => $users), 'admin');

  }

  // Show most recent entries in the log (not named log to avoid conflict with native PHP function)
  function history() {

    if (isset($this->plugins->log)) {

      // Copying the work of loadView
      $params = array(
        'app'     => $this,
        'session' => $_SESSION,
        'title'   => 'Admin'
      );

      echo $this->twig->render("partials/header.html", $params);
      echo $this->twig->render("partials/admin_menu.html", $params);
      echo $this->plugins->log->view();
      echo $this->twig->render("partials/footer.html", $params);

    }

  }

  // Setup your rat installation
  function setup() {

    if (Admin::count_users() == 0 && isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
      // Do setup

      $user_id = User::add($_POST['email']);
      User::signup($user_id, $_POST['username'], $_POST['password'], $this->config->encryption_salt);

      $user = User::get_by_email($_POST['email']);

      // Update session
      $_SESSION['user_id'] = $user->id;

      // Log login
      if (isset($this->plugins->log)) {
        $this->plugins->log->add($_SESSION['user_id'], 'user', NULL, 'signup');
      }

      Application::flash('success', 'You are now logged in to your app!');

      // Go forth!
      header('Location: ' . $this->url_for('admin', 'config'));

      exit();

    } else {
      // Show setup form

      if (Admin::count_users() == 0) {
        Application::flash('info', 'Welcome to Rat!');
        $this->loadView('admin/setup');
      } else {
        throw new RoutingException($this->uri, "Page not found");
      }

    }

  }

  // Grant access to a beta signup
  function invite() {

    $user = User::get_by_id($_SESSION['user_id']);
    $email = $_POST['email'];

    if ($email != '') {

      // Add invite to database
      $id = Invite::add($_SESSION['user_id'], $email);

      // Log invite
      if (isset($this->plugins->log)) {
        $this->plugins->log->add($_SESSION['user_id'], 'invite', $id, 'admin_add', $email);
      }

      $to      = $email;
      $link    = $this->config->url . 'users/add/' . $id . '/?email=' . urlencode($email);

      // Load template into $body variable
      $to      = array('email' => $email);
      $subject  = '[' . $this->config->name . '] Your ' . $this->config->name . ' invite is here!';
      $body    = $this->twig_string->render(file_get_contents("themes/{$this->config->theme}/emails/admin_invite.html"), array('link' => $link, 'app' => array('config' => $this->config)));

      // Email user
      $this->email->send_email($to, $subject, $body);

      Application::flash('success', 'User invited!');

    }

    $this->signups();

  }

  function grant_invites() {

    if ($this->uri['params']['count'] > 0) {

      Admin::update_invites($this->uri['params']['count']);

      Application::flash('success', 'Invites updated!');

    }

    $this->users();

  }

}
