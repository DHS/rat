<?php

if (!isset($item))
	$item = $app->page->item;

if (count($item['likes']) > 0) {

	echo '<span id="likes_'.$item['id'].'">
	<p class="meta">'.$app->config->items['likes']['past_tense'].' ';
	
	foreach ($item['likes'] as $like) {

		if (isset($app->plugins->gravatar)) {
			$gravatar = $app->plugins->gravatar->show($like['user']['email'], array('size' => 20, 'style' => "margin-top: -5px;"));
			echo $this->link_to($gravatar, 'users', 'show', $like['user']['id']).' ';
		} else {
			echo $this->link_to($like['user']['username'], 'users', 'show', $like['user']['id']).' ';
		}

	}
	
	echo '</p></span>';
	
} else {
	// no likes yet but print empty div fo das ajax
	
	echo '<span id="likes_'.$item['id'].'"></span>';
	
}

?>