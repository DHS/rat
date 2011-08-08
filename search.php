<?php

require_once 'config/init.php';

// Header

include 'themes/'.$app->theme.'/header.php';

echo 'hi ';

//$a = $item->hi();

$a = $item->list_all();
var_dump($a);

//include 'themes/'.$app->theme.'/items_list.php';

// Footer

include 'themes/'.$app->theme.'/footer.php';

?>