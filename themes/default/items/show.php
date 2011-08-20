
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
	if (isset($app->plugins->gravatar))
		echo $app->plugins->gravatar->show($item['user']['email'], array('link' => "/{$item['user']['username']}", 'size' => 20, 'style' => "margin-bottom: -7px;"));
	?>
	<a href="/<?php echo $item['user']['username']; ?>"><?php echo $item['user']['username']; ?></a>

	&middot; <?php $app->loadView('items_meta'); ?>
	
</p>

<?php
// Untested
if ($app->config->private != TRUE)
	$app->loadView('items/share');
?>

<?php

if ($app->config->items['likes']['enabled'] == TRUE)
	$app->loadView('likes');

if ($app->config->items['comments']['enabled'] == TRUE) {
	$app->page->show_comment_form = TRUE;
	$app->loadView('comments');
}

?>