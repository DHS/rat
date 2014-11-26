<?php

class Item {

  public function __construct(array $attrs = null) {

    if (is_array($attrs)) {
      foreach ($attrs as $key => $value) {
        $this->$key = $value;
      }
    }

  }

  // Create an item, returns item id
  public static function add($user_id, $content, $title = NULL, $image = NULL) {

    global $mysqli;
    $config = new Config;

    $user_id = sanitize_input($user_id);
    $content = sanitize_input($content);

    $sql = "INSERT INTO `{$config->database->{$config->site_identifier}->prefix}items` SET `user_id` = $user_id, `content` = $content";

    if ($title != NULL) {
      $title = sanitize_input($title);
      $sql .= ", title = $title";
    }

    if ($image != NULL) {
      $image = sanitize_input($image);
      $sql .= ", image = $image";
    }

    $query = mysqli_query($mysqli, $sql);

    return mysqli_insert_id($mysqli);

  }

  // Get an item by id, returns an Item object
  public static function get_by_id($id) {

    global $mysqli;
    $config = new Config;

    $id = sanitize_input($id);

    $sql = "SELECT `id`, `user_id`, `title`, `content`, `image`, `date` FROM `{$config->database->{$config->site_identifier}->prefix}items` WHERE `id` = $id ORDER BY `id` DESC";
    $query = mysqli_query($mysqli, $sql);
    $result = mysqli_fetch_assoc($query);

    if ( ! is_array($result)) {

      return $item;

    } else {

      $item = new Item($result);

      $item->date = date('c',strtotime($item->date));
      $item->user = $item->user();
      $item->comments = $item->comments();
      $item->likes = $item->likes();

      unset($item->user->password);

      return $item;

    }

  }

  // Get recent items, returns array of Item objects
  public static function list_all($limit = 10, $offset = 0) {

    global $mysqli;
    $config = new Config;

    $sql = "SELECT `id` FROM `{$config->database->{$config->site_identifier}->prefix}items` ORDER BY `id` DESC";

    // Limit string
    $limit = sanitize_input($limit);
    $sql .= " LIMIT $limit";

    // Offset string
    $offset = sanitize_input($offset);
    $sql .= " OFFSET $offset";

    $query = mysqli_query($mysqli, $sql);

    // Loop through item ids, fetching objects
    $items = array();
    if ($query != false) {
      while ($query && $result = mysqli_fetch_assoc($query)) {
        $items[] = Item::get_by_id($result['id']);
      }
    }

    return $items;

  }

  // Update an item
  public function update($title, $content) {

    global $mysqli;
    $config = new Config;

    $sql = "UPDATE `{$config->database->{$config->site_identifier}->prefix}items` SET ";

    if ($title != '') {
      $title = sanitize_input($title);
    } else {
      $title = 'NULL';
    }
    $sql .= "`title` = $title, ";

    if ($content != '') {
      $content = sanitize_input($content);
    } else {
      $content = 'NULL';
    }
    $sql .= "`content` = $content";

    $sql .= " WHERE `id` = $this->id";

    $query = mysqli_query($mysqli, $sql);

  }

  // Get the user for an item, returns a User object
  public function user() {

    return User::get_by_id($this->user_id);

  }

  // Get comments for an item, returns an array of Comment objects
  public function comments($limit = 10, $offset = 0) {

    global $mysqli;
    $config = new Config;

    $sql = "SELECT `id` FROM `{$config->database->{$config->site_identifier}->prefix}comments` WHERE `item_id` = $this->id ORDER BY `id` ASC";

    // Limit string
    $limit = sanitize_input($limit);
    $sql .= " LIMIT $limit";

    // Offset string
    $offset = sanitize_input($offset);
    $sql .= " OFFSET $offset";

    $query = mysqli_query($mysqli, $sql);

    $comments = array();
    while ($query && $result = mysqli_fetch_assoc($query)) {
      $comments[] = Comment::get_by_id($result['id']);
    }

    return $comments;

  }

  // Get likes for an item, returns an array of Like objects
  public function likes($limit = 10, $offset = 0) {

    global $mysqli;
    $config = new Config;

    $sql = "SELECT `id` FROM `{$config->database->{$config->site_identifier}->prefix}likes` WHERE `item_id` = $this->id";

    // Limit string
    $limit = sanitize_input($limit);
    $sql .= " LIMIT $limit";

    // Offset string
    $offset = sanitize_input($offset);
    $sql .= " OFFSET $offset";

    $query = mysqli_query($mysqli, $sql);

    $likes = array();
    while ($query && $result = mysqli_fetch_assoc($query)) {
      $likes[$result['id']] = Like::get_by_id($result['id']);
    }

    return $likes;

  }

  // Remove an item, returns item id
  public function remove() {

    global $mysqli;
    $config = new Config;

    // Check item exists
    $sql_check = "SELECT `id` FROM `{$config->database->{$config->site_identifier}->prefix}items` WHERE `id` = $this->id";
    $count_query = mysqli_query($mysqli, $sql_check);

    if (mysqli_num_rows($count_query) > 0) {
      // If item exists, go ahead and delete
      $sql_delete = "DELETE FROM `{$config->database->{$config->site_identifier}->prefix}items` WHERE `id` = $this->id";
      $query = mysqli_query($mysqli, $sql_delete);
    }

  }

}
