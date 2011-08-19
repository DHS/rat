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

if ($app->page->show_comment_form == TRUE) {

	if (!isset($item))
		$item = $app->page->item;
	
	if ($app->config->items['comments']['enabled'] == TRUE && ($app->config->private == TRUE || $_SESSION['user'] != NULL)) {
	
	?>
	
		<form action="javascript:comment_add(<?php echo $item['id']; ?>, '/<?php echo $item['user']['username']; ?>/<?php echo $app->config->items['name']; ?>/<?php echo $item['id']; ?>/comment/add');" id="comment_form_<?php echo $item['id']; ?>" class="meta" style="margin: 0px; <?php if ($app->page->show_comment_form != TRUE) { echo 'visibility: hidden; height: 0px;'; }?>" method="post">
			<input type="text" name="content" size="30" value="" /> <input type="submit" value="<?php echo $app->config->items['comments']['name']; ?>" />
		</form>
	
	<?php
	
	}

}

?>
