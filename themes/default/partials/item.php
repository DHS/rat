<?php

// Populate some vars

// Image
if ($this->config->items['uploads']['enabled'] == TRUE && $this->item->image != NULL && $this->item->image != '0') {
	$image = '<a href="' . $this->url_for('items', 'show', $this->item->id) . '"><img src="' . BASE_DIR . $this->config->items['uploads']['directory'].'/stream/' . $this->item->image . '" style="margin: 0px 0px 10px 0px;" /></a>';
	if ($this->item->title != NULL) {
		$image = '<div class="clear">' . $image . '</div>';
	}
} else {
	$image = '';
}

// Avatar
if (isset($this->plugins->gravatar)) {
	$gravatar_pic = $this->plugins->gravatar->show($this->item->user->email, array('size' => 48, 'style' => 'float: left; padding: 0px 10px 5px 0px;'));
	$gravatar = $this->get_link_to($gravatar_pic, 'users', 'show', $this->item->user->id) . ' ';
} else {
	$gravatar = $this->get_link_to($like->user->username, 'users', 'show', $this->item->user->id) . ' ';
}

// Title & content
if ($this->config->items['titles']['enabled'] == TRUE && $this->item->title != NULL) {
	$title = '<h4>' . $this->get_link_to($this->item->title, 'items', 'show', $this->item->id) . ' <small>by ' . $this->get_link_to($this->item->user->username, 'users', 'show', $this->item->user->id) . '</small></h4>';
	$content = '<p class="clear">' . $this->item->content.'</p>';
} else {
	$title = '';
	if ($this->item->content != NULL) {
		$content = '<p><strong>' . $this->get_link_to($this->item->user->username, 'users', 'show', $this->item->user->id) . '</strong> ' . $this->item->content . '</p>';
	} else {
		$content = '';
	}
}

// Comment form toggle
if (count($this->item->comments) > 0) {
	$this->show_comment_form = TRUE;
} else {
	$this->show_comment_form = FALSE;
}

// Find if current user likes the item
$i_like = FALSE;
if (is_array($this->item->likes)) {
	foreach ($this->item->likes as $value) {
		if (isset($_SESSION['user_id']) && $value->user->id == $_SESSION['user_id']) {
			$i_like = TRUE;
		}
	}
}

?>

    <!-- Begin item -->

    <!-- Content -->
    <?php echo $gravatar; ?>
	<?php echo $title; ?>
	<?php echo $image; ?>
	<?php echo $content; ?>

	<?php if ($this->config->private == FALSE) { ?>
	    <span style="float: right; margin-left: 20px;">
	      <a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo substr($this->config->url, 0, -1).$this->url_for('items', 'show', $this->item->id); ?>" data-text="<?php echo $this->item->title; ?>" data-count="none">Tweet</a><script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
	      <iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo substr($this->config->url, 0, -1).$this->url_for('items', 'show', $this->item->id); ?>&amp;layout=button_count&amp;show_faces=true&amp;width=100&amp;action=like&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>
	    </span>
	<?php } ?>

    <!-- Meta -->
	<p class="item_meta">
	<?php $this->link_to($this->item->date, 'items', 'show', $this->item->id);

	if ($this->config->items['comments']['enabled'] == TRUE) {
		echo ' &middot <a href="#" onclick="document.getElementById(\'comment_form_' . $this->item->id . '\').style.visibility = \'visible\'; document.getElementById(\'comment_form_' . $this->item->id . '\').style.height = \'auto\'; document.getElementById(\'comment_form_' . $this->item->id . '\').content.focus(); return false;">' . $this->config->items['comments']['name'].'</a>';
	}

	if ($this->config->items['likes']['enabled'] == TRUE) {
		if ($i_like == TRUE) {
			echo ' &middot <span id="like_link_' . $this->item->id . '"><a href="#" onclick="like_remove(\'' . BASE_DIR . '\', ' . $this->item->id . ', \''.str_replace("'", "\'", $this->config->items['likes']['name']) . '\', \''.str_replace("'", "\'", $this->config->items['likes']['opposite_name']) . '\'); return false;">' . $this->config->items['likes']['opposite_name'].'</a></a></span>';
		} else {
			echo ' &middot <span id="like_link_' . $this->item->id . '"><a href="#" onclick="like_add(\'' . BASE_DIR . '\', ' . $this->item->id . ', \''.str_replace("'", "\'", $this->config->items['likes']['name']) . '\', \''.str_replace("'", "\'", $this->config->items['likes']['opposite_name']) . '\'); return false;">' . $this->config->items['likes']['name'].'</a></a></span>';
		}

	}

	if (isset($_SESSION['user_id']) && $this->item->user->id == $_SESSION['user_id']) {
		echo ' &middot; <a onclick="return confirm(\'Are you sure you want to delete this?\')" href="' . $this->url_for('items', 'remove', $this->item->id) . '">Delete</a>';
	}
?>
	</p>

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
