
<span class="item_meta">

<?php echo $this->link_to($this->item->date, 'items', 'show', $item->id); ?>

<?php

if ($this->config->items['comments']['enabled'] == TRUE)
	echo ' &middot <a href="#" onclick="document.getElementById(\'comment_form_'.$item->id.'\').style.visibility = \'visible\'; document.getElementById(\'comment_form_'.$item->id.'\').style.height = \'auto\'; document.getElementById(\'comment_form_'.$item->id.'\').content.focus(); return false;">'.$this->config->items['comments']['name'].'</a>';

if ($this->config->items['likes']['enabled'] == TRUE) {
	
	$i_like = FALSE;
	
	// find if current user likes the item
	if (is_array($item->likes)) {
		foreach ($item->likes as $value) {
			if ($value['user']['id'] == $_SESSION['user']['id'])
				$i_like = TRUE;
		}
	}
	if ($i_like == TRUE) {
		$url = $this->link_to(NULL, 'likes', 'remove', $item->id);
		echo ' &middot <span id="like_link_'.$item->id.'"><a href="#" onclick="like_remove('.$item->id.', \''.$url.'\'); return false;">'.$this->config->items['likes']['opposite_name'].'</a></a></span>';
	} else {
		$url = $this->link_to(NULL, 'likes', 'add', $item->id);
		echo ' &middot <span id="like_link_'.$item->id.'"><a href="#" onclick="like_add('.$item->id.', \''.$url.'\'); return false;">'.$this->config->items['likes']['name'].'</a></a></span>';
	}
	unset ($i_like);

}

if ($item->user->id == $_SESSION['user']['id']) {
	$url = $this->link_to(NULL, 'items', 'remove', $item->id);
	echo ' &middot; <a onclick="return confirm(\'Are you sure you want to delete this?\')" href="'.$url.'">Delete</a>';
}

?>

</span>
