<?php

class LikesController extends Application {

  protected $requireLoggedIn = array('add', 'remove');

  function add($item_id) {

    $like_id = Like::add($_SESSION['user_id'], $item_id);

    $item = Item::get_by_id($item_id);

    if ($item->user->email_notifications['item_like']) {

      $to       = array('name' => $item->user->username, 'email' => $item->user->email);
      $subject  = '[' . $this->config->name . '] Someone clicked ' . strtolower($this->config->items['likes']['name']) . ' on your ' . strtolower($this->config->items['name']) . ' on ' . $this->config->name . '!';
      $body     = $this->twig_string->render(file_get_contents("themes/{$this->config->theme}/emails/item_like.html"), array('link' => substr($this->config->url, 0, -1) . $this->url_for('items', 'show', $item->id), 'app' => array('config' => $this->config), 'user' => $item->user));

      // Email user
      $this->email->send_email($to, $subject, $body);

    }

    if (isset($this->plugins->log)) {
      $this->plugins->log->add($_SESSION['user_id'], 'like', $like_id, 'add');
    }

    $this->show($item_id);

  }

  function remove($item_id) {

    $like = Like::get_by_user_item($_SESSION['user_id'], $item_id);

    $like->remove();

    if (isset($this->plugins->log)) {
      $this->plugins->log->add($_SESSION['user_id'], 'like', $like->id, 'remove');
    }

    $this->show($item_id);

  }

  private function show($item_id) {

    $item = Item::get_by_id($item_id);
    $item->content = process_content($item->content);

    // Copying the work of loadView
    $params = array(
      'app'     => $this,
      'session' => $_SESSION,
      'item'    => $item
    );

    echo $this->twig->render("partials/likes.html", $params);

  }

  function json($item_id) {

    $item = Item::get_by_id($item_id);
    $this->json = $item->likes;
    $this->loadView('pages/json', NULL, 'none');

  }

}
