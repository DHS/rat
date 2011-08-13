
<?php if ($app->items['titles']['enabled'] == TRUE) { ?>

<p style="font-size: 200%; line-height: 100%; width: 500px; text-align: left;"><?php echo $item['title']; ?></p>
<?php if ($app->items['uploads']['enabled'] == TRUE && $item['image'] != NULL) { ?>
	<a href="<?php echo $app->items['uploads']['directory']; ?>/originals/<?php echo $item['image']; ?>"><img src="<?php echo $app->items['uploads']['directory']; ?>/stream/<?php echo $item['image']; ?>" /></a>
<?php } ?>
<p><?php echo $item['content'] ?></p>

<?php } else { ?>

<p style="font-size: 200%; line-height: 100%; width: 500px; text-align: left;"><?php echo $item['content']; ?></p>  

<?php } ?>

<p>

	<?php
	if (isset($GLOBALS['gravatar']))
		echo $GLOBALS['gravatar']->show($item['user']['email'], array('user_id' => $item['user']['id'], 'size' => 20, 'style' => "margin-bottom: -7px;"));
	?>
	<a href="user.php?id=<?php echo $item['user']['id']; ?>"><?php echo $item['user']['username']; ?></a>

	&middot; <?php $app->loadView('items_meta'); ?>
	
</p>

<?php
// Untested
if ($app->private != TRUE)
	$app->loadView('items_share');
?>

<?php

if ($app->items['likes']['enabled'] == TRUE)
	$app->loadView('likes_list');

if ($app->items['comments']['enabled'] == TRUE) {
	$app->loadView('comments_list');
	$show_comment_form = TRUE;
	$app->loadView('comments_add');
}

?>