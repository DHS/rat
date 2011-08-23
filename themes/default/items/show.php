
<?php $item = $app->page->item; ?>

<div class="row">
  <div class="span8 columns offset4">

<?php if ($app->config->items['titles']['enabled'] == TRUE) { ?>

<h2><?php echo $item['title']; ?></h2>

<?php if ($app->config->items['uploads']['enabled'] == TRUE && $item['image'] != NULL) { ?>
	<a href="<?php echo $app->config->items['uploads']['directory']; ?>/originals/<?php echo $item['image']; ?>"><img src="<?php echo $app->config->items['uploads']['directory']; ?>/stream/<?php echo $item['image']; ?>" /></a>
<?php } ?>

<p><?php echo $item['content'] ?></p>

<?php } else { ?>

<p><?php echo $item['content']; ?></p>

<?php } ?>

<p>

<?php
if (isset($app->plugins->gravatar)) {
	$gravatar = $app->plugins->gravatar->show($item['user']['email'], array('size' => 20, 'style' => "margin-top: -5px;"));
	echo $this->link_to($gravatar, 'users', 'show', $item['user']['id']).' ';
}
?>

<?php echo $this->link_to($item['user']['username'], 'users', 'show', $item['user']['id']); ?>

&middot; <?php $app->loadView('items/meta'); ?>

</p>

<?php
// Untested
if ($app->config->private != TRUE) {
	$app->loadView('items/share');
}
?>

<?php

if ($app->config->items['likes']['enabled'] == TRUE) {
	$app->loadView('likes/index');
}

if ($app->config->items['comments']['enabled'] == TRUE) {
	$app->page->show_comment_form = TRUE;
	$app->loadView('comments/index');
}

?>

  </div>
</div>
