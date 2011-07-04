<?php

require_once 'config/init.php';

/* Header */

$app['page_name'] = 'Help';
include 'themes/'.$GLOBALS['app']['theme'].'/header.php';

/* Show help page content */

include 'themes/'.$GLOBALS['app']['theme'].'/help.php';

/* Footer */

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>