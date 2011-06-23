<?php

require_once 'config/init.php';

/* Header */

$page['name'] = 'Help';
include 'themes/'.$GLOBALS['app']['theme'].'/header.php';

/* Show help page content */

//include 'themes/'.$GLOBALS['app']['theme'].'/help.php';

/* Building visual config */

//echo "<pre>App\n\n";
//var_dump($GLOBALS['app']);
//echo '</pre>';

/* Footer */

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>