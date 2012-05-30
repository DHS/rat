<?php

class Invite {

  public function __construct(array $attrs = null) {

    if (is_array($attrs)) {
      foreach ($attrs as $key => $value) {
        $this->$key = $value;
      }
    }

  }

  // Add an invite, returns invite id
  public static function add($user_id, $email) {

    global $mysqli;
    $config = new AppConfig;

    $user_id = sanitize_input($user_id);
    $email = sanitize_input($email);

    $insert_sql = "INSERT INTO `{$config->database[SITE_IDENTIFIER]['prefix']}invites` SET `user_id` = $user_id, `email` = $email";
    $insert_query = mysqli_query($mysqli, $insert_sql);

    $id = mysqli_insert_id($mysqli);

    $update_sql = "UPDATE `{$config->database[SITE_IDENTIFIER]['prefix']}invites` SET `code` = '$id' WHERE `id` = $id";
    $query = mysqli_query($mysqli, $update_sql);

    return $id;

  }

  // Get a single invite, returns an Invite object
  public static function get_by_id($id) {

    global $mysqli;
    $config = new AppConfig;

    $id = sanitize_input($id);

    $sql = "SELECT `id`, `user_id`, `email`, `code`, `result`, `date` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}invites` WHERE `id` = $id";
    $query = mysqli_query($mysqli, $sql);
    $result = mysqli_fetch_assoc($query);

    if ( ! is_array($result)) {
      // Invite not found

      return null;

    } else {

      $invite = new Invite($result);
      $invite->user = User::get_by_id($result['user_id']);

      unset($invite->user->password);

      return $invite;

    }

  }

  // Get all invites with a given code, returns an array of Invite objects
  public static function list_by_code($code, $limit = 10, $offset = 0) {

    global $mysqli;
    $config = new AppConfig;

    $code = sanitize_input($code);

    $sql = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}invites` WHERE `code` = $code";

    // Limit string
    $limit = sanitize_input($limit);
    $sql .= " LIMIT $limit";

    // Offset string
    $offset = sanitize_input($offset);
    $sql .= " OFFSET $offset";

    $query = mysqli_query($mysqli, $sql);

    // Loop through invite ids, fetching objects
    $invites = array();
    while ($result = mysqli_fetch_assoc($query)) {
      $invites[] = Invite::get_by_id($result['id']);
    }

    return $invites;

  }

  // Update an invite
  public function update() {

    global $mysqli;
    $config = new AppConfig;

    $sql = "SELECT `result` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}invites` WHERE `id` = $this->id";
    $query = mysqli_query($mysqli, $sql);
    $invites = mysql_fetch_assoc($query);

    $invites['result']++;

    // Update database
    $sql = "UPDATE `{$config->database[SITE_IDENTIFIER]['prefix']}invites` SET `result` = {$invites['result']} WHERE `id` = $this->id";
    $query = mysqli_query($mysqli, $sql);

  }

  // Checks to see if a user is already invited, returns TRUE or FALSE
  public static function check_invited($user_id, $email) {

    global $mysqli;
    $config = new AppConfig;

    $user_id = sanitize_input($user_id);
    $email = sanitize_input($email);

    $sql = "SELECT COUNT(id) AS count FROM `{$config->database[SITE_IDENTIFIER]['prefix']}invites` WHERE `user_id` = $user_id AND `email` = $email";
    $query = mysqli_query($mysqli, $sql);
    $user = mysqli_fetch_assoc($query);

    if ($user['count'] >= 1) {

      return true;

    } else {

      return false;

    }

  }

  // Validates an invite code, returns TRUE or FALSE
  public static function check_code_valid($code, $email) {

    global $mysqli;
    $config = new AppConfig;

    if ($code == '') {
      return false;
    }

    $code = sanitize_input($code);
    $email = sanitize_input($email);

    $sql = "SELECT `result` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}invites` WHERE `code` = $code AND `email` = $email";
    $query = mysqli_query($mysqli, $sql);
    $status = mysqli_num_rows($query);

    if ($status > 0) {
      return true;
    } else {
      return false;
    }

  }

}
