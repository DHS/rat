<?php

class Filter {

  public function __construct(&$app) {
    $this->app = $app;
  }

  public function requireLoggedIn($uri, $actions) {

    if (in_array($uri['action'], $actions) && ! isset($_SESSION['user_id'])) {
      Application::flash('error', 'You must be logged in!');
      $this->app->redirect_to('sessions', 'add');
      exit;
    }

  }

  public function requireLoggedOut($uri, $actions) {

    if (in_array($uri['action'], $actions) && isset($_SESSION['user_id'])) {
      Application::flash('error', 'You are already logged in!');
      $this->app->redirect_to('items');
      exit;
    }

  }

  public function requireInvitesEnabled($uri, $actions) {

    if (in_array($uri['action'], $actions) && $this->app->config->invites->enabled != TRUE) {
      Application::flash('error', 'Page not found');
      $this->app->redirect_to('items');
    }

  }

  public function requireAdmin($uri, $actions) {

    if (in_array($uri['action'], $actions) && (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_id'], $this->app->config->admin_users))) {
      Application::flash('error', 'Page not found');
      $this->app->redirect_to('items');
    }

  }

  // Add your own filters to run before each action is loaded

}
