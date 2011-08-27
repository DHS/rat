<?php

// Start session
session_start();

// Load exceptions
$exceptions = glob('lib/exceptions/*.php' );
foreach ($exceptions as $exception) {
	require($exception);
}

require_once 'lib/application.php';

Application::initialise();

?>
