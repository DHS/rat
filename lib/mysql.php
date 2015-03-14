<?php

/**
 * SQL injection protection function
 */
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
