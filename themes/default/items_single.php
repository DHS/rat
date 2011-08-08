
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
	if (is_object($GLOBALS['gravatar']))
		echo $GLOBALS['gravatar']->show($item['user']['email'], array('user_id' => $item['user']['id'], 'size' => 20, 'style' => "margin-bottom: -7px;"));
	?>
	<a href="user.php?id=<?php echo $item['user']['id']; ?>"><?php echo $item['user']['username']; ?></a>

	&middot; <?php include 'items_meta.php'; ?>
	
</p>

<?php
// Untested
if ($app->private != TRUE)
	include 'themes/'.$app->theme.'/items_share.php';
?>

<?php

if ($app->items['likes'] == TRUE)
	include 'themes/'.$app->theme.'/likes_list.php';

if ($app->items['comments'] == TRUE) {
	include 'themes/'.$app->theme.'/comments_list.php';
	$comments_add_show = TRUE;
	include 'themes/'.$app->theme.'/comments_add.php';
}

?>