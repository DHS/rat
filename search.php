<?php

require_once 'config/init.php';

// Header

include 'themes/'.$app->theme.'/header.php';

include 'themes/'.$app->theme.'/search.php';

if (isset($_GET['q'])) {
	$items = $app->search->do_search($_GET['q']);
	include 'themes/'.$app->theme.'/items_list.php';
}

// Footer

include 'themes/'.$app->theme.'/footer.php';

?>