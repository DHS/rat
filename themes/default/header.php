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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!--

Built by David Haywood Smith:
http://twitter.com/DHS

Powered by Rat:
http://github.com/DHS/rat

-->

<title><?php echo $head_title; ?></title>

<link rel="stylesheet" type="text/css" href="themes/default/styles.css" />

<script src="assets/js/javascript.js" type="text/javascript"></script>

<?php
if (is_object($GLOBALS['analytics']))
	echo $GLOBALS['analytics']->show();

?>

</head>
<body>

<div id="header">

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

</div>

<!-- Content div -->
<div id="content">

<!-- Page title -->
<div class="center_container">
<?php

if (is_object($GLOBALS['gravatar']) && !empty($app['page_title_gravatar'])) {
	// Show gravatar

echo '<table class="center">
<tr>
<td>';
echo $GLOBALS['gravatar']->show($app['page_title_gravatar'], array('style' => "margin-right: 10px;"));
echo '</td>
<td><h1>'.$page_title.'</h1></td>
</tr>
</table>';

} else {

	echo '<h1>'.$page_title.'</h1>';

}

?>

</div>

<!-- Title spacer -->
<p>&nbsp;</p>

<?php

if ($_GET['message'] != '') {
	$message = $_GET['message'];
}

if ($message) {

	echo '<!-- Message -->
<p class="message">'.$message.'</p>';
}

?>