<?php

require_once 'config/init.php';

// Header

include 'themes/'.$app->theme.'/header.php';

$app->item->list_search($_GET['q']);

include 'themes/'.$app->theme.'/items_list.php';

// Footer

include 'themes/'.$app->theme.'/footer.php';

?>