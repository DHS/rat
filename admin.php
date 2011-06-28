<?php

require_once 'config/init.php';

// Critical: Setup wizard creates admin as first user

if (count(admin_get_users()) == 0 && $_GET['page'] == '') {
	
	$page['name'] = 'Setup';
	
	$_GET['id'] = 1;
	$password = generate_password();
	
	$message = 'Welcome to Rat! Please enter your details:';
	include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
	include 'themes/'.$GLOBALS['app']['theme'].'/admin_user_add.php';
	include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';
	
	exit();
	
} elseif (count(admin_get_users()) == 0 && $_GET['page'] == 'invite') {
	
	$page['name'] = 'Setup';
	
	// Do signup

	$user_id = user_add($_POST['email']);
	user_signup($user_id, $_POST['username'], $_POST['password']);
	
	$user = user_get_by_email($_POST['email']);
	$_SESSION['user'] = $user;
	
	// Log login
	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_SESSION['user']['id'], 'user', NULL, 'signup');
	
	$message = 'You are now logged in! Set up your app by tweaking the following variables:';
	
	// Go forth!
	if (SITE_IDENTIFIER == 'live') {
		header('Location: '.$GLOBALS['app']['url'].'admin.php?page=config&message='.urlencode($message));
	} else {
		header('Location: '.$GLOBALS['app']['dev_url'].'admin.php?page=config&message='.urlencode($message));
	}
	
	exit();
	
}

//	Critical: User must have admin capability

if (in_array($_SESSION['user']['id'], $app['admin_users']) != TRUE) {

	$page['name'] = 'Page not found';
	include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
	include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';
	exit;

}

/*	
*	Admin page functions
*
*		1. Dashboard
*		2. List beta signups
*		3. Invite beta signups
*		4. List users
*		5. Give users invites
*	
*/

function dashboard() {
	
	$user_count = count(admin_get_users());
	$waiting_user_count = count(admin_get_waiting_users());
	
	// Show header
	$page['name'] = 'Admin - '.ucfirst(strtolower($_GET['page']));
	include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
	include 'themes/'.$GLOBALS['app']['theme'].'/admin_menu.php';
	
	// Show dashboard
	include 'themes/'.$GLOBALS['app']['theme'].'/admin_dashboard.php';
	
}

function config() {
	// Updates config

	//echo "<pre>POST:\n\n";
	//var_dump($_POST);
	//echo '</pre>';
	//
	//echo "<pre>GLOBALS:\n\n";
	//var_dump($GLOBALS['app']);
	//echo '</pre>';
	
	//// Finds available plugins
	//$handle = opendir('plugins');
	//while (false !== ($file = readdir($handle))) {
	//	if ($file[0] != '.') {
	//		$plugin = substr($file, 0, -4);
	//		echo "$plugin<br />";
	//	}
	//}
	
	if (!empty($_POST)) {

		// Loop through GLOBAL variables
		foreach ($GLOBALS['app'] as $key => $value) {
			
			if (is_array($value)) {
				// Global var is an array
								
				foreach ($value as $key2 => $value2) {
					
					$new_value = $_POST[$key][$key2];
					
					if ($new_value != $value2) {
												
						//echo "$key [ $key2 ] = $value2 &rarr; $new_value";
						//
						//echo $key;
						//echo " = ";
						//var_dump($value2);
						//echo " &rarr; ";
						//var_dump($new_value);
						
						if (is_bool($value2)) {
							// If original value is a bool then check for checked ('on') vs unchecked (null) new values
							
							//echo $key.' - '.$key2;
							
							if ($new_value == 'on') {
								$new_value = TRUE;
							} elseif ($new_value == 'FALSE' || isset($new_value) == FALSE) {
								$new_value = FALSE;
							}
							
						}
						
						if (is_numeric($value2))
							$new_value = $new_value*1;
						
						//echo "(actually: ";
						//var_dump($new_value);
						//echo ")<br />";
												
						$GLOBALS['app'][$key][$key2] = $new_value;
						
					}

					$to_update[$key2] = $GLOBALS['app'][$key][$key2];
					
				}
					
				// Create json string
				$json = serialize($to_update);
                
				// Sanitize input
				$key = sanitize_input($key);					
				$json = sanitize_input($json);
                
				// Update single value in database
				$sql = "UPDATE options SET option_value = {$json} WHERE option_name = $key";
				//echo "Update SQL: $sql<br />";
				$query = mysql_query($sql);
                
				$message = 'Config updated!';

				unset($to_update);
				
			} else {
				// This global var is not an array
				
				$new_value = $_POST[$key];
				
				if ($new_value != $value) {
					// Posted var doesn't equal global value
					
					//var_dump($key);
					//echo " = ";
					//var_dump($value);
					//echo " &rarr; ";
					//var_dump($new_value);
					
					if (is_bool($value)) {
						// If value is a bool then check for checked ('on') vs unchecked (null)
						
						if ($new_value == 'on') {
							$new_value = TRUE;
						} elseif ($new_value == FALSE || isset($new_value) == FALSE) {
							$new_value = FALSE;
						}
						
					}
					
					if (is_numeric($value2))
						$new_value = $new_value*1;
					
					$GLOBALS['app'][$key] = $new_value;

					//echo ", actually: ";
					//var_dump($new_value);
					//echo "<br />";
					
					$new_value = serialize($new_value);
					
					// Sanitize database input
					$key = sanitize_input($key);
					$new_value = sanitize_input($new_value);
					
					// Update single value in database
					$sql = "UPDATE options SET option_value = $new_value WHERE option_name = $key";
					//echo "$sql<br /><br />";
					$query = mysql_query($sql);
					
					$message = 'Config updated!';
					
				}
								
			}
			
		}
		
	}

	/*

	// If vars have been posted then parse
	if (!empty($_POST)) {

		// Loop through posted variables
		foreach ($_POST as $key => $value) {
			
			// Ignore 'page'
			if ($key != 'page') {
				
				// If magic quotes is enabled then strip slashes on the key
				if (get_magic_quotes_gpc())
					$key = stripslashes($key);
				
				// Is the submitted field an array of values
				if (is_array($value)) {
				
					// Loop through each submitted field
					foreach ($value as $key2 => $value2) {

    	    			// Strip slashes if necessary
						if (get_magic_quotes_gpc())
							$key2 = stripslashes($key2);

						if (get_magic_quotes_gpc())
							$value2 = stripslashes($value2);

    	    			// If submitted value is different to existing value (GLOBAL) then add to $updates array
						if ($GLOBALS['app'][$key][$key2] != $value2)
							$updates[$key][$key2] = $value2;
					
					}
					
					// Updates were detected
					if (is_array($updates[$key])) {
						
						// Fetch unchanged parts of the array
						$total_keys = array_keys($GLOBALS['app'][$key]);
						$updated_keys = array_keys($updates[$key]);
						$remaining = array_diff($total_keys, $updated_keys);
    	                
						// Loop through unchanged parts, fetching values
						foreach ($remaining as $key2 => $value2) {
							$updates[$key][$value2] = $GLOBALS['app'][$key][$value2];
						}
						
						//$updates[$key] = array_merge($updates[$key], $GLOBALS['app'][$key]);
						//
						//foreach ($updates[$key] as $key2 => $value2) {
						//	if ($value == 'true') {
						//		$value == '"TRUE"';
						//	} elseif ($value == 'false') {
						//		$value == '"FALSE"';
						//	}
						//	$updates[$key][$key2] = $value2;
						//}
						
						$GLOBALS['app'][$key] = $updates[$key];
						
						// Create json string
						$json = json_encode($updates[$key]);

						print_r($json);

						// Sanitize database input
						$key = sanitize_input($key);
						$json = sanitize_input($json);

						// Add json string to database
						$sql = "UPDATE options SET option_value = $json WHERE option_name = $key";
						$query = mysql_query($sql);
						
						$message = 'Config updated!';
						
					}
					
				} else {
					// Single value detected
					
					// Strip slashes if necessary
					if (get_magic_quotes_gpc())
						$value = stripslashes($value);
					
					// Add to $updates array
					if ($GLOBALS['app'][$key] != $value) {
						
						$GLOBALS['app'][$key] = $value;
						
						if ($value == 'true') {
							$value = '"TRUE"';
						} elseif ($value == 'false') {
							$value = '"FALSE"';
						}
						
						echo "$key: $value, ";
						
						// Sanitize database input
						$key = sanitize_input($key);
						$value = sanitize_input($value);
						
						// Update single value in database
						$sql = "UPDATE options SET option_value = $value WHERE option_name = $key";
						$query = mysql_query($sql);
						
						$message = 'Config updated!';
						
					}
				
				}
			
			}
			
		}
		
	}
	
	*/
	
	// Show header
	$page['name'] = 'Admin - '.ucfirst(strtolower($_GET['page']));
	include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
	include 'themes/'.$GLOBALS['app']['theme'].'/admin_menu.php';

	// Show admin form
	include 'themes/'.$GLOBALS['app']['theme'].'/admin_config.php';
	
}


function signups() {
	
	$waiting_users = admin_get_waiting_users();
	$waiting_user_count = count($waiting_users);
	
	// Show header
	$page['name'] = 'Admin - '.ucfirst(strtolower($_GET['page']));
	include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
	include 'themes/'.$GLOBALS['app']['theme'].'/admin_menu.php';
	
	// Show signups
	include 'themes/'.$GLOBALS['app']['theme'].'/admin_signups.php';
	
}

function invite() {

	if ($_GET['email'] != '') {
		
		// add invite to database
		$id = invites_add($_SESSION['user']['id'], $_GET['email']);
		
		// log invite
		if (is_object($GLOBALS['log']))
			$GLOBALS['log']->add($_SESSION['user']['id'], 'invite', $id, 'admin_add', $_GET['email']);
		
		if (SITE_IDENTIFIER == 'live') {
			$to		= "{$_POST['username']} <{$_GET['email']}>";
		} else {
			$to		= "{$_POST['username']} <davehs@gmail.com>";
		}
		
		$url		= $GLOBALS['app']['url'].'signup.php?code='.$id.'&email='.urlencode($_GET['email']);
		
		$subject	= "Your {$GLOBALS['app']['name']} invite is here!";
		$body		= "Hi there,\n\nYour {$GLOBALS['app']['name']} invite is here! Click the following link to get started:\n\n{$url}\n\nWe value your feedback very highly. Once you've had a play with {$GLOBALS['app']['name']}, please reply to this email with your thoughts!\n\nMany thanks,\n\n{$_SESSION['user']['username']}, {$GLOBALS['app']['name']} admin";
		$headers	= "From: {$_SESSION['user']['username']} <{$_SESSION['user']['email']}>";
		
		if ($GLOBALS['app']['send_emails'] == TRUE) {
			// Email user
			mail($to, $subject, $body, $headers);
		}
		
		$message = 'User invited!';
		include 'themes/'.$GLOBALS['app']['theme'].'/message.php';
		
		signups();
		
	}
	
}

function users() {
	
	$users = admin_get_users();
	$user_count = count($users);
	
	// Show header
	$page['name'] = 'Admin - '.ucfirst(strtolower($_GET['page']));
	include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
	include 'themes/'.$GLOBALS['app']['theme'].'/admin_menu.php';
	
	// Show users
	include 'themes/'.$GLOBALS['app']['theme'].'/admin_users.php';

}

function grant_invites() {
			
	if ($_GET['count'] > 0) {
		
		admin_grant_invites($_GET['count']);
		
		$message = 'Invites updated!';
		include 'themes/'.$GLOBALS['app']['theme'].'/message.php';
		
		users();
		
	}
	
}

function generate_password() {
	$password = '';
	$source = 'abcdefghijklmnpqrstuvwxyz123456789';
	while (strlen($password) < 6) {
		$password .= $source[rand(0, strlen($source))];
	}
	return $password;
}


/* Selector */

$page['selector'] = $_GET['page'];
if ($page['selector'] == NULL) {
	$page['selector'] = 'dashboard';
}

/* Header */

//$page['name'] = 'Admin - '.ucfirst(strtolower($page));
//include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
//include 'themes/'.$GLOBALS['app']['theme'].'/admin_menu.php';

/* Show page determined by selector */

$page['selector']();

/* Footer */

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>