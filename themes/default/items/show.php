
<?php $item = $this->item; ?>

<div class="row">
  <div class="span8 columns offset4">

<?php if ($this->config->items['titles']['enabled'] == TRUE) { ?>

<h2><?php echo $item->title; ?></h2>

<?php if ($this->config->items['uploads']['enabled'] == TRUE && $item->image != NULL) { ?>
	<a href="<?php echo $this->config->items['uploads']['directory']; ?>/originals/<?php echo $item->image; ?>"><img src="<?php echo $this->config->items['uploads']['directory']; ?>/stream/<?php echo $item->image; ?>" /></a>

<?php } ?>

<p><?php echo $item->content; ?></p>

<?php } else { ?>

<p><?php echo $item->content; ?></p>

<?php } ?>

<p>

<?php
if (isset($this->plugins->gravatar)) {
	$gravatar = $this->plugins->gravatar->show($item->user->email, array('size' => 20, 'style' => "margin-top: -5px;"));
	echo $this->link_to($gravatar, 'users', 'show', $item->user->id).' ';
}
?>

<?php echo $this->link_to($item->user->username, 'users', 'show', $item->user->id); ?>

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
