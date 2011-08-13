<?php

require_once 'config/init.php';

// Header

$app->loadView('header');

$app->loadView('search');

if (isset($_GET['q'])) {
	$app->page->items = $app->search->do_search($_GET['q']);
	$app->loadView('items_list');
}

// Footer

$app->loadView('footer');

?>