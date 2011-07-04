<?php

require_once 'config/initialize.php';

/* Header */

$app['page_name'] = $app['tagline'];
include 'themes/'.$GLOBALS['app']['theme'].'/header.php';

/* Show intro paragraph */

//include 'themes/'.$GLOBALS['app']['theme'].'/index.php';

/* Show new item form */

include 'themes/'.$GLOBALS['app']['theme'].'/items_new.php';

/* Show all recent items */

$items = items_get();
include 'themes/'.$GLOBALS['app']['theme'].'/items_index.php';

/* Footer */

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>