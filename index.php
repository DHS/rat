<?php

require_once 'config/init.php';

// Header

$page['name'] = $app['tagline'];
include 'themes/'.$GLOBALS['app']['theme'].'/header.php';

// Intro paragraph

//include 'themes/'.$GLOBALS['app']['theme'].'/index.php';

// New item form

include 'themes/'.$GLOBALS['app']['theme'].'/items_new.php';

// Show recent items

$items = items_get();
include 'themes/'.$GLOBALS['app']['theme'].'/items_index.php';

// Footer

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>