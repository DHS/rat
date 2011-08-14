<?php

if (!isset($item))
	$item = $app->page->item;

if (count($item['likes']) > 0) {

	echo '<span id="likes_'.$item['id'].'">
	<p class="meta">'.$app->config->items['likes']['past_tense'].' ';
	
	foreach ($item['likes'] as $like) {

		if (isset($app->plugins->gravatar)) {
			echo $app->plugins->gravatar->show($like['user']['email'], array('link' => "/{$like['user']['username']}", 'size' => 20, 'style' => "margin-bottom: -5px;")).' ';
		} else {
			echo '<a href="/'.$like['user']['username'].'">';
			echo $like['user']['username'];
			echo '</a> ';
		}

	}
	
	echo '</p></span>';
	
} else {
	// no likes yet but print empty div fo das ajax
	
	echo '<span id="likes_'.$item['id'].'"></span>';
	
}

?>