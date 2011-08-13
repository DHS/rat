<?php

require_once 'config/init.php';

// Header

$app->page->name = 'About';
$app->loadView('header');

// Show About page

foreach ($app->config->admin_users as $value) {
	$author = $app->user->get($value);
	if ($app->config->private != TRUE || $_SESSION['user'] != NULL) {
		$authors .= '<a href="user.php?id='.$author['id'].'">'.$author['username'].'</a>, ';
	} else {
		$authors .= $author['username'].', ';
	}

}
$authors = substr($authors, 0, -2);


$content = '<p>'.$app->config->name.' is a web app created by '.$authors.' based on the <a href="http://github.com/DHS/rat">rat</a> framework. ';

if ($app->config->beta == TRUE)
	$content .= 'It is currently in beta.';

$content .= "</p>\n";

$content .= '<p>It lets you create '.$app->config->items['name_plural'];

if ($app->config->items['titles']['enabled'] == TRUE)
	$content .= ' with '.strtolower($app->config->items['titles']['name_plural']);

if ($app->config->items['comments']['enabled'] == TRUE || $app->config->items['likes']['enabled'] == TRUE) {

	$content .= ' and then ';

	if ($app->config->items['comments']['enabled'] == TRUE)
		$content .= ' add '.strtolower($app->config->items['comments']['name_plural']).' ';
	
	if ($app->config->items['comments']['enabled'] == TRUE && $app->config->items['likes']['enabled'] == TRUE)
		$content .= ' and ';
	
	if ($app->config->items['likes']['enabled'] == TRUE)
		$content .= ' \''.strtolower($app->config->items['likes']['name']).'\' ';
	
	$content .= 'them';
	
}

$content .= ". </p>\n";

if ($app->config->invites['enabled'] == TRUE)
	$content .= "<p>It also has an invite system so that you can invite your friends.</p>\n";

if (isset($app->plugins->points)) {
	
	$content .= '<p>It also has a points system';
	
	if ($app->plugins->points['leaderboard'] == TRUE)
		$content .= ' and a leaderboard so you can see how you\'re doing relative to everyone else';
	
	$content .= ".</p>\n";
	
}

if (isset($app->plugins->gravatar))
	$content .= '<p>'.$app->config->name.' is <a href="http://gravatar.com/">Gravatar</a>-enabled.</p>'."\n";

echo $content;

// Footer

$app->loadView('footer');

?>