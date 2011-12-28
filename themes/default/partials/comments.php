<span id="comments_<?php echo $this->item->id; ?>">

<?php

if (count($this->item->comments) > 0) {
	
	foreach ($this->item->comments as $comment) {
		
		echo '<p class="meta">
		"'.$comment->content.'" - '.$this->get_link_to($comment->user->username, 'users', 'show', $comment->user->id);
		if (isset($_SESSION['user_id']) && $comment->user->id == $_SESSION['user_id']) {
			echo ' &middot; <span class="small"><a href="#" onclick="comment_remove(\''.BASE_DIR.'\', '.$comment->id.', '.$this->item->id.'); return false;">Delete</a></span>';
		}
		echo '</p>';
		
	}
	
}

?>

<?php if ($this->config->items['comments']['enabled'] == TRUE && isset($_SESSION['user_id'])) { ?>

  <form action="javascript:comment_add('<?php echo BASE_DIR; ?>', <?php echo $this->item->id; ?>);" id="comment_form_<?php echo $this->item->id; ?>" class="meta" style="margin: 0px; <?php if ($this->show_comment_form != TRUE) { echo 'visibility: hidden; height: 0px;'; } ?>" method="post">
    <input type="text" name="content" id="content" size="30" value="" /> <input type="submit" value="<?php echo $this->config->items['comments']['name']; ?>" class="btn" />
  </form>

<?php } ?>

</span>