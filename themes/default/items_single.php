
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
		echo $app->plugins->gravatar->show($item['user']['email'], array('user_id' => $item['user']['id'], 'size' => 20, 'style' => "margin-bottom: -7px;"));
	?>
	<a href="user.php?id=<?php echo $item['user']['id']; ?>"><?php echo $item['user']['username']; ?></a>

	&middot; <?php include 'items_meta.php'; ?>
	
</p>

<?php
// Untested
if ($app->config->private != TRUE)
	include 'themes/'.$app->config->theme.'/items_share.php';
?>

<?php

if ($app->config->items['likes']['enabled'] == TRUE)
	include 'themes/'.$app->config->theme.'/likes_list.php';

if ($app->config->items['comments']['enabled'] == TRUE) {
	include 'themes/'.$app->config->theme.'/comments_list.php';
	$show_comment_form = TRUE;
	include 'themes/'.$app->config->theme.'/comments_add.php';
}

?>