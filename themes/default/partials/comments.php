<span id="comments_<?php echo $this->item->id; ?>">

<?php

if (count($this->item->comments) > 0) {
	
	foreach ($this->item->comments as $comment) {
		
		echo '<p class="meta">
		"'.$comment->content.'" - '.$this->link_to($comment->user->username, 'users', 'show', $comment->user->id);
		if ($comment->user->id == $_SESSION['user']['id']) {
			$url = $this->link_to(NULL, 'comments', 'remove', $comment->id);
			echo ' &middot; <span class="small"><a href="#" onclick="comment_remove('.$this->item->id.', \''.$url.'\'); return false;">Delete</a></span>';
		}
		echo '</p>';
		
	}

}

?>

</span>

<?php if ($this->config->items['comments']['enabled'] == TRUE && ($this->config->private == TRUE || $_SESSION['user'] != NULL)) { ?>

  <form action="javascript:comment_add(<?php echo $this->item->id; ?>, '<?php echo $this->link_to(NULL, 'comments', 'add'); ?>');" id="comment_form_<?php echo $this->item->id; ?>" class="meta" style="margin: 0px; <?php if ($this->show_comment_form != TRUE) { echo 'visibility: hidden; height: 0px;'; } ?>" method="post">
    <input type="text" name="content" size="30" value="" /> <input type="submit" value="<?php echo $this->config->items['comments']['name']; ?>" class="btn" />
  </form>

<?php } ?>