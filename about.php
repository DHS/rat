<?php

require_once 'config/init.php';

// Header

$page['name'] = 'About';
include 'themes/'.$app->theme.'/header.php';

// Show About page

foreach ($GLOBALS['app']['admin_users'] as $value) {
	$author = $user->get($value);
	if ($GLOBALS['app']->private'] != TRUE || $_SESSION['user'] != NULL) {
		$authors .= '<a href="user.php?id='.$author['id'].'">'.$author['username'].'</a>, ';
	} else {
		$authors .= $author['username'].', ';
	}

}
$authors = substr($authors, 0, -2);


$content = '<p>'.$GLOBALS['app']->name.' is a web app created by '.$authors.' based on the <a href="http://github.com/DHS/rat">rat</a> framework. ';

if ($GLOBALS['app']->beta'] == TRUE)
	$content .= 'It is currently in beta.';

$content .= "</p>\n";

$content .= '<p>It lets you create '.$GLOBALS['app']['items']['name_plural'];

if ($GLOBALS['app']['items']['titles']['enabled'] == TRUE)
	$content .= ' with '.strtolower($GLOBALS['app']['items']['titles']['name_plural']);

if ($GLOBALS['app']['items']['comments'] == TRUE || $GLOBALS['app']['items']['likes'] == TRUE) {

	$content .= ' and then ';

	if ($GLOBALS['app']['items']['comments']['enabled'] == TRUE)
		$content .= ' add '.strtolower($GLOBALS['app']['items']['comments']['name_plural']).' ';
	
	if ($GLOBALS['app']['items']['comments']['enabled'] == TRUE && $GLOBALS['app']['items']['likes']['enabled'] == TRUE)
		$content .= ' and ';
	
	if ($GLOBALS['app']['items']['likes']['enabled'] == TRUE)
		$content .= ' \''.strtolower($GLOBALS['app']['items']['likes']['name']).'\' ';
	
	$content .= 'them';
	
}

$content .= ". </p>\n";

if ($GLOBALS['app']['invites']['enabled'] == TRUE)
	$content .= "<p>It also has an invite system so that you can invite your friends.</p>\n";

if (is_object($GLOBALS['points'])) {
	
	$content .= '<p>It also has a points system';
	
	if ($GLOBALS['app']['points']['leaderboard'] == TRUE)
		$content .= ' and a leaderboard so you can see how you\'re doing relative to everyone else';
	
	$content .= ".</p>\n";
	
}

if (is_object($GLOBALS['gravatar']))
	$content .= '<p>'.$GLOBALS['app']->name.' is <a href="http://gravatar.com/">Gravatar</a>-enabled.</p>'."\n";

echo $content;

// Footer

include 'themes/'.$app->theme.'/footer.php';

?>