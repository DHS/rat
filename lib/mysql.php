<?php

$db = $this->config->environments->{$this->config->site_identifier}->database;

// Create database connection
$mysqli = new mysqli(
  $db->host,
  $db->username,
  $db->password,
  $db->database
);

if ($mysqli == false) {
  throw new ApplicationException($this, "Couldn't connect to server.");
}

// SQL injection protection function

function sanitize_input($input) {

  global $mysqli;

  if (get_magic_quotes_gpc()) {
    $input = stripslashes($input);
  }

  // If not a number, then add quotes
  if ( ! is_numeric($input)) {
    $input = "'" . mysqli_real_escape_string($mysqli, $input) . "'";
  }

  return $input;

}
