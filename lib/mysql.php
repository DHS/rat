<?php

/**
 * SQL injection protection function
 */
function sanitize_input($input, $skipQuotes = false) {

  global $mysqli;

  if (get_magic_quotes_gpc()) {
    $input = stripslashes($input);
  }

  if (is_numeric($input) || $skipQuotes) {
    $input = mysqli_real_escape_string($mysqli, $input);
  } else {
    $input = "'" . mysqli_real_escape_string($mysqli, $input) . "'";
  }

  return $input;

}
