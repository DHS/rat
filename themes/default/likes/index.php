<?php

if (count($item->likes) > 0) {

	echo '<span id="likes_'.$item->id.'">
	<p class="meta">'.$this->config->items['likes']['past_tense'].' ';
	
	foreach ($item->likes as $like) {

		if (isset($this->plugins->gravatar)) {
			$gravatar = $this->plugins->gravatar->show($like->user->email, array('size' => 20, 'style' => ""));
			echo $this->link_to($gravatar, 'users', 'show', $like->user->id).' ';
		} else {
			echo $this->link_to($like->user->username, 'users', 'show', $like->user->id).' ';
		}

	}
	
	echo '</p></span>';
	
} else {
	// no likes yet but print empty div fo das ajax
	
	echo '<span id="likes_'.$item->id.'"></span>';
	
}

?>