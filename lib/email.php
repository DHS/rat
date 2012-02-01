<?php

class Email {

	public function __construct(&$app) {
		$this->app = $app;
	}
	
	// Helper function to convert raw input to html
	public function send_email($to, $subject, $body, $debug = FALSE) {
		
		if ($this->app->config->send_emails == TRUE) {
			
			$admin = User::get_by_id($this->app->config->admin_users[0]);
			
			if ($to['name']) {
				$to = "{$to['name']} <{$to['email']}>";
			} elseif ($to['email']) {
				$to = $to['email'];
			}
			
			$headers = "From: {$this->app->config->send_emails_from}\r\nBcc: {$admin->email}\r\nContent-type: text/html\r\n";
			
			if ($debug == TRUE) {
				$to = htmlentities($to);
				echo "$to<br />$subject<br />$body<br />$headers";
			} else {
				mail($to, $subject, $body, $headers);
			}
			
		}
		
	}

}

?>