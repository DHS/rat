<?php

require_once 'config/init.php';

// Header

$app->page->name = 'Help';
$app->loadView('header');

// Content

$app->loadView('help');

// Footer

$app->loadView('footer');

?>