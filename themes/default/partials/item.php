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
    <?php $this->loadPartial('item_meta'); ?>

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
