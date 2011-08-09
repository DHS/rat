<?php

require_once 'config/init.php';

// Critical: user can't already be logged in

if (!empty($_SESSION['user'])) {
	
	$page['name'] = 'Signup';
	$message = 'You are already logged in!';
	include 'themes/'.$app->theme.'/header.php';
	include 'themes/'.$app->theme.'/footer.php';
	exit;
	
}


/*
*	Signup functions
*		
*		What it does							function name
*		
*		Show signup form						show_form
*		Validate a code							validate_code
*		Signup (beta/with invite code/full)		do_signup
*
*	Here's how signup works:
*	
*		If app in beta then users can only enter email then must await invite code
*		If app out of beta then invite codes still work
*
*	Here's how invite codes work:
*	
*		Users can invite friends if invites > 0 in their profile
*		The invite points to signup.php?code=foo&email=bar
*		The code + email must match in invites table (unless no longer in beta)
*
*/

// Show signup form
function show_form() {
	
	global $app;
	
	if ($app->beta == TRUE) {
		
		// Show beta signup form
		include 'themes/'.$app->theme.'/signup_beta.php';
		
	} else {
		
		// Show full signup form
		include 'themes/'.$app->theme.'/signup.php';
		
	}

}

// Validate a code
function validate_code() {

	global $app;

	if ($app->user->validate_invite_code($_GET['code'], $_GET['email']) == TRUE) {
		// Valid

		include 'themes/'.$app->theme.'/signup.php';
		
	} else {
		// Invalid
		
		if ($app->beta == TRUE) {
			$message = 'Your invite code is invalid.';
			include 'themes/'.$app->theme.'/message.php';
			include 'themes/'.$app->theme.'/signup_beta.php';
		} else {
			include 'themes/'.$app->theme.'/signup.php';
		}

	}
	
}

// Glorious new signup function
function do_signup($mode = 'full') {

	global $app;

	/*
	*	Three modes			Code	Email	Username	Password	user->add	user->signup	invites	points
	*		1. beta					X								X
	*		2. code			X		X		X			X			if needed	X			X		X
	*		3. full					X		X			X			X			X			X		X
	*
	*/
	
	// Check invite code (only really matters if app is in beta)

	if ($mode == 'code' && $app->beta == TRUE) {

		if ($app->user->validate_invite_code($_POST['code'], $_POST['email']) != TRUE)
			$error .= 'Invalid invite code.<br />';

	}
	
	// Check email
	
	$_POST['email'] = trim($_POST['email']);
	
	if ($_POST['email'] == '')
		$error .= 'Email cannot be left blank.<br />';
	
	if ($app->user->check_contains_spaces($_POST['email']) == TRUE)
		$error .= 'Email cannot contain spaces.<br />';
	
	if ($app->user->check_contains_at($_POST['email']) != TRUE)
		$error .= 'Email must contain an @ symbol.<br />';
	
	if ($app->user->check_email_available($_POST['email']) != TRUE)
		$error .= 'Email already in the system!<br />';
	
	// Check username
	
	if ($mode == 'code' || $mode == 'full') {
		
		if ($_POST['username'] == '')
			$error .= 'Username cannot be left blank.<br />';
		
		if ($app->user->check_alphanumeric($_POST['username']) != TRUE)
			$error .= 'Username must only contain letters and numbers.<br />';
		
		if ($app->user->check_username_available($_POST['username']) != TRUE)
			$error .= 'Username not available.<br />';
		
	}
	
	// Check password
	
	if ($mode == 'code' || $mode == 'full') {
	
		if ($_POST['password1'] == '' || $_POST['password1'] == '')
			$error .= 'Please enter your password twice.<br />';
		
		if ($_POST['password1'] != $_POST['password2'])
			$error .= 'Passwords do not match.<br />';
		
	}
	
	// Error processing

	if ($error == '') {
		// No error so proceed...
		
		// First check if user added
		$user = $app->user->get_by_email($_POST['email']);

		// If not then add
		if ($user == NULL) {
			
			$user_id = $app->user->add($_POST['email']);
			$user = $app->user->get($user_id);
			
		}
		
		// Do full signup
		if ($mode == 'code' || $mode == 'full') {
		
			// Do signup
			$app->user->signup($user['id'], $_POST['username'], $_POST['password1']);
			
			if ($app->send_emails == TRUE) {
				// Send 'thank you for signing up' email

				$to = "{$_POST['username']} <{$_POST['email']}>";
				$headers = "From: David Haywood Smith <davehs@gmail.com>\r\nBcc: davehs@gmail.com\r\nContent-type: text/html\r\n";

				// Load subject and body from template
				include 'themes/'.$app->theme.'/email_signup.php';

				// Email user
				mail($to, $subject, $body, $headers);

			}
			
			// Log signup
			if (is_object($GLOBALS['log']))
				$GLOBALS['log']->add($user['id'], 'user', NULL, 'signup');
			
			// Start session
			$_SESSION['user'] = $user;
			
			// Check invites are enabled and the code is valid
			if ($app->invites['enabled'] == TRUE && validate_invite_code($_POST['code'], $_POST['email']) == TRUE) {
				
				// Get invites
				$invites = invites_get_by_code($_POST['code']);

				if (is_array($invites)) {
					foreach ($invites as $invite) {
						
						// Update invites
						$app->invite->update($invite['id']);
						
						// Log invite update
						if (is_object($GLOBALS['log']))
							$GLOBALS['log']->add($_SESSION['user']['id'], 'invite', $invite['id'], 'accept');
						
						// Update points (but only if inviting user is not an admin)
						if (is_object($GLOBALS['points']) && in_array($invite['user_id'], $app->admin_users) != TRUE) {
							
							// Update points
							$GLOBALS['points']->update($invite['user_id'], $app->points['per_invite_accepted']);
							
							// Log points update
							if (is_object($GLOBALS['log']))
								$GLOBALS['log']->add($invite['user_id'], 'points', NULL, $app->points['per_invite_accepted'], 'invite_accepted = '.$invite['id']);
							
						}
						
					}
					// end foreach
				}
				// end if is_array
				
			}
			
			// Log login
			if (is_object($GLOBALS['log']))
				$GLOBALS['log']->add($_SESSION['user']['id'], 'user', NULL, 'login');
			
			// If redirect_to is set then redirect
			if ($_GET['redirect_to']) {
				header('Location: '.$_GET['redirect_to']);
				exit();
			}
			
			// Set welcome message
			$message = urlencode('Welcome to '.$GLOBALS['app']->name.'!');
			
			// Go forth!
			if (SITE_IDENTIFIER == 'live') {
				header('Location: '.$app->url.'?message='.$message);
			} else {
				header('Location: '.$app->dev_url.'?message='.$message);
			}
	
			exit();
			
		}
		
		// Do beta signup
		if ($mode == 'beta') {
			
			// Log beta signup
			if (is_object($GLOBALS['log']))
				$GLOBALS['log']->add($user_id, 'user', NULL, 'beta_signup', $_POST['email']);
			
			// Set thank you & tweet this message
			$message = 'Thanks for signing up!<br /><br />We\'d be very grateful if you could help spread the word:<br /><br />';
			$message .= '<a href="http://twitter.com/share" class="twitter-share-button" data-url="http://ScribeSub.com/" data-text="I just signed up to the ScribeSub beta!" data-count="none" data-via="ScribeSubHQ" data-related="DHS:Creator of ScribeSub">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
	
			//include 'themes/'.$app->theme.'/message.php';
			
			// Go forth!
			if (SITE_IDENTIFIER == 'live') {
				header('Location: '.$app->url.'?message='.$message);
			} else {
				header('Location: '.$app->dev_url.'?message='.$message);
			}
	
			exit();
			
		}
	
	} else {
		// There was an error
		
		// Propagate get vars to be picked up by the form
		$_GET['email']		= $_POST['email'];
		$_GET['username']	= $_POST['username'];
		$_GET['code']		= $_POST['code'];
		
		// Commented out while objectifying $app
		//$app = $GLOBALS['app'];
		
		// Show error message
		$message = $error;
		include 'themes/'.$app->theme.'/header.php';
		
		// Show relevant signup form
		if ($mode == 'beta') {
			include 'themes/'.$app->theme.'/signup_beta.php';
		} else {
			include 'themes/'.$app->theme.'/signup.php';
		}
	
	}
	
}

// Selector

$mode = NULL;

if ($_GET['code'] != '') {
	
	$page['selector'] = 'validate_code';
	
} elseif ($_POST['email'] != '') {
	
	if ($_POST['code'] != '') {
		
		$page['selector'] = 'do_signup';
		$mode = 'code';
		
	} else {
		
		if ($app->beta == TRUE) {
			
			$page['selector'] = 'do_signup';
			$mode = 'beta';
			
		} else {
			
			$page['selector'] = 'do_signup';
			$mode = 'full';
			
		}
		
	}
	
} else {
	
	$page['selector'] = 'show_form';
	
}

//var_dump($page['selector']);
//var_dump($mode);

// Show page determined by selector

if ($page['selector'] == 'do_signup') {
	// Do signup. No headers to allow redirects. Error pages load their own page header.
	
	do_signup($mode);
	
} else {
	// Not doing signup so show a simpler page. Also call header.
	
	include 'themes/'.$app->theme.'/header.php';
	$page['selector']();
	
}

// Footer

include 'themes/'.$app->theme.'/footer.php';


?>