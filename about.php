<?php

require_once 'config/init.php';

/* Header */

$app['page_name'] = 'About';
include 'themes/'.$GLOBALS['app']['theme'].'/header.php';

/* Show About page */

foreach ($GLOBALS['app']['admin_users'] as $value) {
	$author = user_get_by_id($value);
	if ($GLOBALS['app']['private'] != TRUE || $_SESSION['user'] != NULL) {
		$authors .= '<a href="user.php?id='.$author['id'].'">'.$author['username'].'</a>, ';
	} else {
		$authors .= $author['username'].', ';
	}

}
$authors = substr($authors, 0, -2);

include 'themes/'.$GLOBALS['app']['theme'].'/about.php';

/* Footer */

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>