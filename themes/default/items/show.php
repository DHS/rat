
<div class="row">
  <div class="span8 columns offset4">

<?php if ($this->config->items['titles']['enabled'] == TRUE) { ?>

<h2><?php echo $this->item->title; ?></h2>

<?php if ($this->config->items['uploads']['enabled'] == TRUE && $this->item->image != NULL) { ?>
	<a href="<?php echo $this->config->items['uploads']['directory']; ?>/originals/<?php echo $this->item->image; ?>"><img src="<?php echo $this->config->items['uploads']['directory']; ?>/stream/<?php echo $this->item->image; ?>" /></a>

<?php } ?>

<p><?php echo $this->item->content; ?></p>

<?php } else { ?>

<p><?php echo $this->item->content; ?></p>

<?php } ?>

<p>

<?php
if (isset($this->plugins->gravatar)) {
	$gravatar = $this->plugins->gravatar->show($this->item->user->email, array('size' => 20, 'style' => "margin-top: -5px;"));
	echo $this->link_to($gravatar, 'users', 'show', $this->item->user->id).' ';
}
?>

<?php echo $this->link_to($this->item->user->username, 'users', 'show', $this->item->user->id); ?>

&middot; <?php $this->loadView('items/meta'); ?>

</p>

<?php
// Untested
if ($this->config->private != TRUE)
	$this->loadView('items/share');
?>

<?php

if ($this->config->items['likes']['enabled'] == TRUE)
	$this->loadView('likes/index');

if ($this->config->items['comments']['enabled'] == TRUE) {
	$this->show_comment_form = TRUE;
	$this->loadView('comments/index');
}

?>

  </div>
</div>
