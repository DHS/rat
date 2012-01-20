<?php

// Helper function to convert raw input to html
function send_email($to, $subject, $body) {
	
	$to			= "{$to['name']} <{$to['email']}>";
	$headers	= "From: {$user->username} <{$user->email}>\r\nContent-type: text/html\r\n";
	
	mail($to, $subject, $body, $headers);
	
}

?>