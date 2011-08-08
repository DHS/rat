<?php

require_once 'config/init.php';

// Header

$page['name'] = 'Help';
include 'themes/'.$app->theme.'/header.php';

// Content

include 'themes/'.$app->theme.'/help.php';

// Footer

include 'themes/'.$app->theme.'/footer.php';

?>