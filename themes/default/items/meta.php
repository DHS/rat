
<?php if (!isset($item))
	$item = $app->page->item; ?>

<span class="item_meta">

<?php echo $this->link_to($item['date'], 'items', 'show', $item['id']); ?>

<?php

if ($app->config->items['comments']['enabled'] == TRUE)
	echo ' &middot <a href="#" onclick="document.getElementById(\'comment_form_'.$item['id'].'\').style.visibility = \'visible\'; document.getElementById(\'comment_form_'.$item['id'].'\').style.height = \'auto\'; document.getElementById(\'comment_form_'.$item['id'].'\').content.focus(); return false;">'.$app->config->items['comments']['name'].'</a>';

if ($app->config->items['likes']['enabled'] == TRUE) {
	
	$i_like = FALSE;
	
	// find if current user likes the item
	if (is_array($item['likes'])) {
		foreach ($item['likes'] as $value) {
			if ($value['user']['id'] == $_SESSION['user']['id'])
				$i_like = TRUE;
		}
	}
	if ($i_like == TRUE) {
		echo ' &middot <span id="like_link_'.$item['id'].'"><a href="#" onclick="like_remove('.$item['id'].', \'/'.$item['user']['username'].'/'.$app->config->items['name'].'/'.$item['id'].'/like/remove\'); return false;">'.$app->config->items['likes']['opposite_name'].'</a></a></span>';
	} else {
		echo ' &middot <span id="like_link_'.$item['id'].'"><a href="#" onclick="like_add('.$item['id'].', \'/'.$item['user']['username'].'/'.$app->config->items['name'].'/'.$item['id'].'/like/add\'); return false;">'.$app->config->items['likes']['name'].'</a></a></span>';
	}
	unset ($i_like);

}

if ($item['user']['id'] == $_SESSION['user']['id'])
	echo ' &middot; <a onclick="return confirm(\'Are you sure you want to delete this?\')" href="item.php?delete='.$item['id'].'">Delete</a>';

?>

</span>
