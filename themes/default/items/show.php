
<?php $item = $app->page->item; ?>

<?php if ($app->config->items['titles']['enabled'] == TRUE) { ?>

<p style="font-size: 200%; line-height: 100%; width: 500px; text-align: left;"><?php echo $item['title']; ?></p>
<?php if ($app->config->items['uploads']['enabled'] == TRUE && $item['image'] != NULL) { ?>
	<a href="<?php echo $app->config->items['uploads']['directory']; ?>/originals/<?php echo $item['image']; ?>"><img src="<?php echo $app->config->items['uploads']['directory']; ?>/stream/<?php echo $item['image']; ?>" /></a>
<?php } ?>
<p><?php echo $item['content'] ?></p>

<?php } else { ?>

<p style="font-size: 200%; line-height: 100%; width: 500px; text-align: left;"><?php echo $item['content']; ?></p>  

<?php } ?>

<p>

	<?php
	if (isset($app->plugins->gravatar)) {
		$gravatar = $app->plugins->gravatar->show($item['user']['email'], array('size' => 20, 'style' => "margin-top: -5px;"));
		echo $this->link_to($gravatar, 'users', 'show', $item['user']['id']).' ';
	}
	?>
	<?php echo $this->link_to($item['user']['username'], 'users', 'show', $item['user']['id']); ?>

	&middot; <?php $this->loadView('items/meta'); ?>
	
</p>

<?php
// Untested
if ($app->config->private != TRUE)
	$this->loadView('items/share');
?>

<?php

if ($app->config->items['likes']['enabled'] == TRUE)
	$this->loadView('likes/index');

if ($app->config->items['comments']['enabled'] == TRUE) {
	$app->page->show_comment_form = TRUE;
	$this->loadView('comments/index');
}

?>