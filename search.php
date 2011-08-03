<?php

require_once 'config/init.php';

// Header

include 'themes/'.$GLOBALS['app']['theme'].'/header.php';

echo 'hi ';

//$a = $item->hi();

$a = $item->list_all();
var_dump($a);

//include 'themes/'.$GLOBALS['app']['theme'].'/items_list.php';

// Footer

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>