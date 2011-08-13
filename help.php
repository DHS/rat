<?php

require_once 'config/init.php';

// Header

$page['name'] = 'Help';
include 'themes/'.$app->config->theme.'/header.php';

// Content

include 'themes/'.$app->config->theme.'/help.php';

// Footer

include 'themes/'.$app->config->theme.'/footer.php';

?>