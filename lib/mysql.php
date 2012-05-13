<?php

// Create database connection

$mysqli = new mysqli(
  $this->config->database[SITE_IDENTIFIER]['host'],
  $this->config->database[SITE_IDENTIFIER]['username'],
  $this->config->database[SITE_IDENTIFIER]['password'],
  $this->config->database[SITE_IDENTIFIER]['database']
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
