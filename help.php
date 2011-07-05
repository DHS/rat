<?php

require_once 'config/init.php';

// Header

$page['name'] = 'Help';
include 'themes/'.$GLOBALS['app']['theme'].'/header.php';

// Content

include 'themes/'.$GLOBALS['app']['theme'].'/help.php';

// Footer

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>