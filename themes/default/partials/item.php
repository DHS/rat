<?php

// Populate some vars
if (isset($this->plugins->gravatar)) {
	$image = $this->plugins->gravatar->show($this->item->user->email, array('size' => 48, 'style' => 'float: left; padding: 0px 10px 10px 0px;'));
	$gravatar = $this->link_to($image, 'users', 'show', $this->item->user->id).' ';
} else {
	$gravatar = $this->link_to($like->user->username, 'users', 'show', $this->item->user->id).' ';
}

if ($this->config->items['titles']['enabled'] == TRUE && $this->item->title != NULL) {
	$content = '<h4>'.$this->link_to($this->item->title, 'items', 'show', $this->item->id).' <small>by '.$this->link_to($this->item->user->username, 'users', 'show', $this->item->user->id).'</small></h4>';
	$content .= '<p>'.$this->item->content.'</p>';
} else {
	$content = '<p>'.$this->link_to($this->item->user->username, 'users', 'show', $this->item->user->id).' '.$this->item->content.'</p>';
}

// Comment form toggle
if (count($this->item->comments) > 0) {
	$this->show_comment_form = TRUE;
} else {
	$this->show_comment_form = FALSE;
}

?>

    <!-- Begin item -->
    
    <!-- Content -->
    <?php echo $gravatar; ?>
    <?php echo $content; ?>
    
    <!-- Meta -->
	<span class="item_meta">
	<?php echo $this->link_to($this->item->date, 'items', 'show', $this->item->id);
    
	if ($this->config->items['comments']['enabled'] == TRUE) {
		echo ' &middot <a href="#" onclick="document.getElementById(\'comment_form_'.$this->item->id.'\').style.visibility = \'visible\'; document.getElementById(\'comment_form_'.$this->item->id.'\').style.height = \'auto\'; document.getElementById(\'comment_form_'.$this->item->id.'\').content.focus(); return false;">'.$this->config->items['comments']['name'].'</a>';
	}
    
	if ($this->config->items['likes']['enabled'] == TRUE) {
    
		$i_like = FALSE;
    
		// find if current user likes the item
		if (is_array($this->item->likes)) {
			foreach ($this->item->likes as $value) {
				if ($value->user->id == $_SESSION['user']['id']) {
					$i_like = TRUE;
				}
			}
		}
		if ($i_like == TRUE) {
			$url = $this->link_to(NULL, 'likes', 'remove', $this->item->id);
			echo ' &middot <span id="like_link_'.$this->item->id.'"><a href="#" onclick="like_remove('.$this->item->id.', \''.$url.'\'); return false;">'.$this->config->items['likes']['opposite_name'].'</a></a></span>';
		} else {
			$url = $this->link_to(NULL, 'likes', 'add', $this->item->id);
			echo ' &middot <span id="like_link_'.$this->item->id.'"><a href="#" onclick="like_add('.$this->item->id.', \''.$url.'\'); return false;">'.$this->config->items['likes']['name'].'</a></a></span>';
		}
		unset ($i_like);
    
	}
    
	if ($this->item->user->id == $_SESSION['user']['id']) {
		$url = $this->link_to(NULL, 'items', 'remove', $this->item->id);
		echo ' &middot; <a onclick="return confirm(\'Are you sure you want to delete this?\')" href="'.$url.'">Delete</a>';
	}	?>
	</span>

<?php if ($this->config->private != TRUE) { ?>
    <span style="float: right; margin-left: 20px;">
      <a href="http://twitter.com/share" class="twitter-share-button" data-count="none">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
      <iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo $this->config->url.substr($_SERVER['REQUEST_URI'], 1); ?>&amp;layout=button_count&amp;show_faces=true&amp;width=100&amp;action=like&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>
    </span>
<?php } ?>

<?php if ($this->config->items['likes']['enabled'] == TRUE) { ?>
    <!-- Likes -->
    <?php $this->loadPartial('likes'); ?>
<?php } ?>

<?php if ($this->config->items['comments']['enabled'] == TRUE) { ?>
    <!-- Comments -->
    <?php $this->loadPartial('comments'); ?>
<?php } ?>

    <!-- Spacer -->
    <p>&nbsp;</p>

    <!-- End item -->
