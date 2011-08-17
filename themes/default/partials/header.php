<?php
// Set page_name for this format: <h1>App name - $app->page->name</h1>
// Overwrite with $app->page->title (and $app->page->head_title if necessary)
// The next few lines process these and output $head_title (for <title></title>) and $page_title (for <h1></h1>)

// Set the var that is printed in head title
if (isset($app->page->head_title))
	$head_title = $app->page->head_title;

if (isset($app->page->title)) {
	// Page title is set = override!

	// Set the var that prints the page title
	$page_title = $app->page->title;

	// If no head title is found then set head title equal to page title
	if (!isset($head_title))
		$head_title = $app->page->title;

} else {
	// No page title set

	if (isset($app->page->name)) {
		// Page name found
		
		// Set the var that prints the page title
		$page_title = '<a href="/">'.$app->config->name.'</a> - '.$app->page->name;

		// If no head title is found then set head title similar to page title
		if (!isset($head_title))
			$head_title = $app->config->name.' - '.$app->page->name;

	} else {
		// No page name found

		// Set page title to app name
		$page_title = '<a href="/">'.$app->config->name.'</a>';

		// Set head title to app name
		if (!isset($head_title))
			$head_title = $app->config->name;

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
  <link rel="stylesheet" href="themes/<?php echo $app->config->theme; ?>/css/style.css">

  <!-- More ideas for your <head> here: h5bp.com/docs/#head-Tips -->

  <!-- All JavaScript at the bottom, except for Modernizr and Respond.
       Modernizr enables HTML5 elements & feature detects; Respond is a polyfill for min/max-width CSS3 Media Queries
       For optimal performance, use a custom Modernizr build: www.modernizr.com/download/ -->
  <script src="js/libs/modernizr-2.0.min.js"></script>
  <script src="js/libs/respond.min.js"></script>
</head>

<body>

  <div id="container">

    <header>

	<?php

			if (isset($_SESSION['user'])) {

				echo '<a href="/">Home</a> &middot; <a href="/'.$_SESSION['user']['username'].'">My profile</a> &middot; ';

				if (isset($app->plugins->points))
					echo 'You have <strong>'.$_SESSION['user']['points'].'</strong> '.$app->plugins->points['name'].' &middot; ';

				if ($app->config->invites['enabled'] == TRUE)
					echo '<a href="/invites">Invites</a> &middot; ';

				echo '<a href="/settings">Settings</a> &middot; <a href="/help">Help</a> &middot; ';

				if (in_array($_SESSION['user']['id'], $app->config->admin_users) == TRUE)
					echo '<a href="admin">Admin</a> &middot; ';

				echo '<a href="/logout">Logout</a>';

			} else {

				echo '<a href="/signup">Signup</a> &middot; <a href="/login">Login</a> &middot; <a href="/help">Help</a>';

			}

	?>

    </header>

	<p class="clear">&nbsp;</p>

    <!-- Page title -->
    <?php

	if (isset($app->plugins->gravatar) && !empty($app->page->title_gravatar)) {
		// Show gravatar

	echo '<table class="center">
	<tr>
	<td>';
	echo $app->plugins->gravatar->show($app->page->title_gravatar, array('style' => "margin-right: 10px;"));
	echo '</td>
	<td><h1>'.$page_title.'</h1></td>
	</tr>
	</table>';

	} else {

		echo '<h1>'.$page_title.'</h1>';

	}

	?>

<?php

if (isset($_GET['message']))
	$app->page->message = $_GET['message'];

if (isset($app->page->message))
	echo '<!-- Message -->
<p class="message">'.$app->page->message.'</p>';

?>
