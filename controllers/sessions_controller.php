<?php

class SessionsController extends Application {

	protected $requireLoggedIn = array('remove');
	protected $requireLoggedOut = array('add');

  function add() {

    if (isset($_POST['email']) && isset($_POST['password'])) {

      $user = User::get_by_email($_POST['email']);

      if ($user->authenticate($_POST['password'], $this->config->encryption_salt) == TRUE) {

        // Get redirected
        if (isset($this->uri['params']['redirect_to'])) {
          header('Location: ' . $this->uri['params']['redirect_to']);
          exit();
	      }

        // Go forth
        header('Location: ' . $this->config->url);

        exit();

      } else {

        Application::flash('error', 'Something isn\'t quite right. Please try again...');
        $email = $_POST['email'];

      }

    }

		if ( ! isset($_SESSION['user_id'])) {

      if (isset($email)) {
        $this->loadView('sessions/add', array('email' => $email));
      } else {
        $this->loadView('sessions/add');
      }

    } else {

			Application::flash('error', 'You are already logged in! ' . $this->get_link_to('Click here', 'sessions', 'remove').' to logout.');
			$this->loadView();

		}

	}

	function remove() {

		$user = User::get_by_id($_SESSION['user_id']);

		if ($user->deauthenticate() == TRUE) {

			Application::flash('info', 'You are now logged out.');
			Application::redirect_to($this->config->default_controller);

		} else {

			Application::flash('info', 'Nothing to see here.');
			$this->loadView();

		}

	}

}
