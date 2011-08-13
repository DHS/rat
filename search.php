<?php

require_once 'config/init.php';

// Header

include 'themes/'.$app->config->theme.'/header.php';

include 'themes/'.$app->config->theme.'/search.php';

if (isset($_GET['q'])) {
	$app->page->items = $app->search->do_search($_GET['q']);
	include 'themes/'.$app->config->theme.'/items_list.php';
}

// Footer

include 'themes/'.$app->config->theme.'/footer.php';

?>