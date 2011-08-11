<?php

if (count($item['likes']) > 0) {

	echo '<span id="likes_'.$item['id'].'">
	<p class="meta">'.$app->items['likes']['past_tense'].' ';
	
	foreach ($item['likes'] as $like) {

		if (isset($GLOBALS['gravatar'])) {
			echo $GLOBALS['gravatar']->show($like['user']['email'], array('user_id' => $like['user']['id'], 'size' => 20, 'style' => "margin-bottom: -5px;")).' ';
		} else {
			echo '<a href="user.php?id='.$like['user']['id'].'">';
			echo $like['user']['username'];
			echo '</a> ';
		}

	}
	
	echo '</p></span>';
	
} else {
	// no likes yet but print empty div fo das ajax
	
	echo '<span id="likes_'.$item['id'].'"></span>';
	
}
