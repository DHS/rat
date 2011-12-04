<p id="likes_<?php echo $this->item->id; ?>" class="meta">

<?php if (count($this->item->likes) > 0) {

	echo $this->config->items['likes']['past_tense'].': ';
	
	foreach ($this->item->likes as $like) {

		if (isset($this->plugins->gravatar)) {
			$gravatar = $this->plugins->gravatar->show($like->user->email, array('size' => 20));
			echo $this->link_to($gravatar, 'users', 'show', $like->user->id).' ';
		} else {
			echo $this->link_to($like->user->username, 'users', 'show', $like->user->id).' ';
		}

	}

} ?>

</p>