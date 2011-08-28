<?php

ob_start();

// Start session
session_start();

// Load exceptions
$exceptions = glob('lib/exceptions/*.php' );
foreach ($exceptions as $exception) {
	include $exception;
}

require_once 'lib/application.php';

Application::initialise();

ob_end_flush();

?>
