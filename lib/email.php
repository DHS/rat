<?php

// Helper function to convert raw input to html
function send_email($to, $subject, $body, $debug = FALSE) {
	
	global $config;
	echo $config->config->name;
	
	if ($app->config->send_emails == TRUE) {
		
		$admin = User::get_by_id($app->config->admin_users[0]);
		
		if ($to['name']) {
			$to = "{$to['name']} <{$to['email']}>";
		} elseif ($to['email']) {
			$to = $to['email'];
		}
		
		$headers = "From: {$app->config->send_emails_from}\r\nBcc: {$admin->email}\r\nContent-type: text/html\r\n";
		
		if ($debug == TRUE) {
			echo "$to<br />$subject<br />$body<br />$headers";
		} else {
			mail($to, $subject, $body, $headers);
		}
		
	}
	
}

?>