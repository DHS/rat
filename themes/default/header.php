<?php
// Set page_name for this format: '<h1>App name - page_name</h1>'
// Overwrite with page_title

// Set the var that is printed in head title
if ($page['head_title'] != NULL)
	$head_title = $page['head_title'];

if ($page['title'] != '') {
	// Page title is set = override!

	// Set the var that prints the page title
	$page_title = $page['title'];

	// If no head title is found then set head title equal to page title
	if ($head_title == NULL)
		$head_title = $page['title'];

} else {
	// No page title set

	if (isset($page['name'])) {
		// Page name found

		// Set the var that prints the page title
		$page_title = '<a href="index.php">'.$GLOBALS['app']['name'].'</a> - '.$page['name'];

		// If no head title is found then set head title similar to page title
		if ($head_title == NULL)
			$head_title = $GLOBALS['app']['name'].' - '.$page['name'];

	} else {
		// No page name found

		// Set page title to app name
		$page_title = '<a href="index.php">'.$GLOBALS['app']['name'].'</a>';

		// Set head title to app name
		if ($head_title == NULL)
			$head_title = $GLOBALS['app']['name'];

	}
}

?>

<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <!-- Use the .htaccess and remove these lines to avoid edge case issues.
       More info: h5bp.com/b/378 -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title><?php echo $head_title; ?></title>
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

  <!-- CSS: implied media="all" -->
  <link rel="stylesheet" href="themes/<?php echo $GLOBALS['app']['theme']; ?>/css/style.css">

  <!-- More ideas for your <head> here: h5bp.com/docs/#head-Tips -->

  <!-- All JavaScript at the bottom, except for Modernizr and Respond.
       Modernizr enables HTML5 elements & feature detects; Respond is a polyfill for min/max-width CSS3 Media Queries
       For optimal performance, use a custom Modernizr build: www.modernizr.com/download/ -->
  <script src="js/libs/modernizr-2.0.min.js"></script>
  <script src="js/libs/respond.min.js"></script>
</head>

<body>

    <header>

	<?php

			if ($_SESSION['user']) {

				echo '<a href="index.php">Home</a> &middot; <a href="user.php">My profile</a> &middot; ';

				if (is_object($GLOBALS['points']))
					echo 'You have <strong>'.$_SESSION['user']['points'].'</strong> '.$app['points']['name'].' &middot; ';

				if ($GLOBALS['app']['invites']['enabled'] == TRUE)
					echo '<a href="invites.php">Invites</a> &middot; ';

				echo '<a href="settings.php">Settings</a> &middot; <a href="help.php">Help</a> &middot; ';

				if (in_array($_SESSION['user']['id'], $app['admin_users']) == TRUE)
					echo '<a href="admin.php">Admin</a> &middot; ';

				echo '<a href="logout.php">Logout</a>';

			} else {

				echo '<a href="signup.php">Signup</a> &middot; <a href="login.php">Login</a> &middot; <a href="help.php">Help</a>';

			}

	?>

    </header>

  <div id="container">

<?php

if ($_GET['message'] != '') {
	$message = $_GET['message'];
}

if ($message) {

	echo '<!-- Message -->
<p class="message">'.$message.'</p>';
}

?>