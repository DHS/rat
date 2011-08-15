<?php

if (!isset($item))
	$item = $app->page->item;

if (count($item['comments']) > 0) {

	echo '<span id="comments_'.$item['id'].'">';

	foreach ($item['comments'] as $comment) {
		
		echo '
		<p class="meta">
		"'.$comment['content'].'" - <a href="/'.$comment['user']['username'].'">'.$comment['user']['username'].'</a>';
		if ($comment['user']['id'] == $_SESSION['user']['id']) {
			echo ' &middot; <span style="font-size: 50%;"><a href="#" onclick="comment_remove('.$item['id'].', \'/'.$item['user']['username'].'/'.$app->config->items['name'].'/'.$item['id'].'/comment/'.$comment['id'].'/remove\'); return false;">Delete</a></span>';
		}
		echo '
		</p>';
	
	}
	
	echo '</span>';

} else {
	// no comments yet but show empty div fo das ajax

	echo '<span id="comments_'.$item['id'].'"></span>';

}

?>