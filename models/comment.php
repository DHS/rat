<?php

class Comment {

  public function __construct(array $attrs = null) {

    if (is_array($attrs)) {
      foreach ($attrs as $key => $value) {
        $this->$key = $value;
      }
    }

  }

  // Add a comment to an item, returns comment id
  public static function add($user_id, $item_id, $content) {

    global $mysqli;
    $config = new AppConfig;

    $user_id = sanitize_input($user_id);
    $item_id = sanitize_input($item_id);
    $content = sanitize_input($content);

    $sql = "INSERT INTO `{$config->database[SITE_IDENTIFIER]['prefix']}comments` SET `user_id` = $user_id, `item_id` = $item_id, `content` = $content";
    $query = mysqli_query($mysqli, $sql);

    return mysqli_insert_id($mysqli);

  }

  // Get a single comment, returns a Comment object
  public static function get_by_id($id) {

    global $mysqli;
    $config = new AppConfig;

    $id = sanitize_input($id);

    $sql = "SELECT `id`, `user_id`, `item_id`, `content`, `date` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}comments` WHERE `id` = $id";
    $query = mysqli_query($mysqli, $sql);
    $result = mysqli_fetch_assoc($query);

    if ( ! is_array($result)) {
      // Comment not found

      return null;

    } else {

      $comment = new Comment($result);
      $comment->user = User::get_by_id($result['user_id']);

      return $comment;

    }

  }

  // Get all comments, returns an array of Comments objects
  public static function list_all($limit = 10, $offset = 0) {

    global $mysqli;
    $config = new AppConfig;

    $sql = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}comments` ORDER BY `date` DESC";

    // Limit string
    $limit = sanitize_input($limit);
    $sql .= " LIMIT $limit";

    // Offset string
    $offset = sanitize_input($offset);
    $sql .= " OFFSET $offset";

    // Get list of ids
    $query = mysqli_query($mysqli, $sql);

    // Loop through comment ids, fetching objects
    $comments = array();
    while ($result = mysqli_fetch_assoc($query)) {
      $comments[] = Comment::get_by_id($result['id']);
    }

    return $comments;

  }

  // Remove a comment from an item, returns comment id
  public function remove() {

    global $mysqli;
    $config = new AppConfig;

    $count_sql = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}comments` WHERE `id` = {$this->id}";
    $count_query = mysqli_query($mysqli, $count_sql);

    if (mysqli_num_rows($count_query) > 0) {
      $sql = "DELETE FROM `{$config->database[SITE_IDENTIFIER]['prefix']}comments` WHERE `id` = {$this->id}";
      $query = mysqli_query($mysqli, $sql);
    }

    return $this->id;

  }

}
