<?php

class ItemsController extends Application {

  protected $requireLoggedIn = array('add', 'remove');

  // Show stream of everyone's items
  function index() {

    // Page zero so overwrite to 1
    if ( ! isset($this->uri['params']['page'])) {
      $this->uri['params']['page'] = 1;
    }

    // Items per page, change this to test pagination
    $limit = 10;

    if ($this->uri['params']['page'] == 1) {
      $offset = 0;
    } else {
      $offset = ($this->uri['params']['page'] - 1) * $limit;
    }

    $items = Item::list_all($limit, $offset);

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

    if ($this->json) {
      $this->render_json($items);
    } else {
      $this->loadView('items/index', array('items' => $items));
    }

  }

  // Add an item
  function add() {

    if (
      (isset($_POST['content'])
        && $_POST['content'] != '')
      || (
        $this->config->items->titles->enabled == TRUE
        && isset($_POST['title'])
        && $_POST['title'] != ''
      )
      || (
        $this->config->items->uploads->enabled == TRUE
        && isset($_FILES['file']['name'])
        && $_FILES['file']['name'] != '')
      ) {

      $error = '';

      // Form validation

      if ($this->config->items->titles->enabled == FALSE && $_POST['content'] == '') {
        $error .= ucfirst($this->config->items->name) . ' must include ' . strtolower($this->config->items->content->name) . '.<br />';
      }

      if ($this->config->items->uploads->enabled == TRUE && $_FILES['file']['name'] != '') {

        if ($_FILES['file']['error'] > 0) {
          $error .= 'Error code: ' . $_FILES['file']['error'] . '<br />';
        }

        if ( ! in_array($_FILES['file']['type'], $this->config->items->uploads->mime_types)) {
          $error .= 'Invalid file type: ' . $_FILES['file']['type'] . '<br />';
        }

        if ($_FILES['file']['size'] > $this->config->items->uploads->max-size) {
          $error .= 'File too large.<br />';
        }

      }

      // Error processing

      if ($error == '') {
        // No error so proceed...

        if ($this->config->items->uploads->enabled == TRUE && $_FILES['file']['name'] != '') {

          include 'lib/upload.php';
          $filename = upload($_FILES['file'], $this->config->items->uploads->directory);

          $item_id = Item::add($_SESSION['user_id'], $_POST['content'], $_POST['title'], $filename);

        } else {

          $item_id = Item::add($_SESSION['user_id'], $_POST['content'], $_POST['title']);

        }

        // Give points
        if (isset($this->plugins->points)) {
          $this->plugins->points->update($_SESSION['user_id'], $this->plugins->points['per_item']);
        }

        // Log item add
        if (isset($this->plugins->log)) {
          $this->plugins->log->add($_SESSION['user_id'], 'item', $item_id, 'add', "title = {$_POST['title']}\ncontent = {$_POST['content']}");
        }

        Application::flash('success', ucfirst($this->config->items->name) . ' added!');

        // Go forth!
        header('Location: ' . $this->url_for('users', 'show', $_SESSION['user_id']));

        exit();


      } else {
        // There was an error

        // Propagate get vars to be picked up by the form
        $this->uri['params']['title']    = $_POST['title'];
        $this->uri['params']['content']  = $_POST['content'];

        // Show error message
        Application::flash('error', $error);
        $this->loadView('items/add');
        exit();

      }

    } else {

      $this->loadView('items/add');

    }

  }

  // Show a single item
  function show($id) {

    $item = Item::get_by_id($id);
    $item->content = process_content($item->content);
    foreach ($item->comments as $comment) {
      $comment->content = process_content($comment->content);
    }

    if ($this->config->items->titles->enabled == TRUE) {
      $this->head_title = $this->config->name . ' - ' . $item->title;
    }

    if ($this->json) {
      $this->render_json($item);
    } else {
      $this->loadView('items/show', array('item' => $item));
    }

  }

  // Update an item
  function update($id) {

    $item = Item::get_by_id($id);
    $item->content = process_content($item->content);
    foreach ($item->comments as $comment) {
      $comment->content = process_content($comment->content);
    }

    if (isset($_POST['title']) || isset($_POST['content'])) {

      if ( ! isset($_POST['title'])) {
        $_POST['title'] = null;
      }

      if ( ! isset($_POST['content'])) {
        $_POST['content'] = null;
      }

      $item->update($_POST['title'], $_POST['content']);

      Application::flash('success', 'Item updated!');

      // Get redirected
      if (isset($this->uri['params']['redirect_to'])) {
        header('Location: ' . $this->uri['params']['redirect_to']);
        exit();
      }

      $item = Item::get_by_id($id);
      $item->content = process_content($item->content);
      foreach ($item->comments as $comment) {
        $comment->content = process_content($comment->content);
      }

      if ($this->config->items->titles->enabled == TRUE) {
        $this->head_title = $this->config->name . ' - ' . $item->title;
      }

      if ($this->json) {
        $this->render_json($item);
      } else {
        $this->loadView('items/show', array('item' => $item));
      }

    } else {

      $this->loadView('items/update', array('item' => $item));

    }

  }

  // Remove an item
  function remove($item_id) {

    $item = Item::get_by_id($item_id);

    if ($_SESSION['user_id'] == $item->user->id && $item != NULL) {

      // Delete item
      $item->remove();

      // Log item deletion
      if (isset($this->plugins->log)) {
        $this->plugins->log->add($_SESSION['user_id'], 'item', $item->id, 'remove');
      }

      // Delete comments
      if (is_array($item->comments)) {

        foreach ($item->comments as $comment) {

          // Remove comment
          $id = $comment->remove();

          // Log comment removal
          if (isset($this->plugins->log)) {
            $this->plugins->log->add($_SESSION['user_id'], 'comment', $id, 'remove');
          }

        }

      }

      // Delete likes
      if (is_array($item->comments)) {

        foreach ($item->likes as $like) {

          // Remove like
          $id = $like->remove();

          // Log like removal
          if (isset($this->plugins->log)) {
            $this->plugins->log->add($_SESSION['user_id'], 'like', $like->id, 'remove');
          }

        }

      }

      // Set message
      Application::flash('success', ucfirst($this->config->items->name) . ' removed!');

      // Return from whence you came
      header('Location: ' . $_SERVER['HTTP_REFERER']);
      exit();

    } else {
      // Naughtiness = expulsion!

      // Go forth
      header('Location: ' . $this->config->url);

      exit();

    }

  }

  // Show feed of friends' new items
  function feed() {

    if ($this->config->friends->enabled == TRUE || isset($_SESSION['user_id'])) {

      // If friends enabled then show feed of friends' activity

      $user = User::get_by_id($_SESSION['user_id']);

      // Page zero so overwrite to 1
      if ( ! isset($this->uri['params']['page'])) {
        $this->uri['params']['page'] = 1;
      }

      // Items per page, change this to test pagination
      $limit = 10;

      if ($this->uri['params']['page'] == 1) {
        $offset = 0;
      } else {
        $offset = ($this->uri['params']['page'] - 1) * $limit;
      }

      $this->items = $user->list_feed($limit, $offset);

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

      $this->loadView('items/index');

    } else {

      // Friends not enabled so fall back to showing everyone's activity

      $this->index();

    }

  }

}
