
<?php if ($GLOBALS['app']['items']['titles']['enabled'] == TRUE) { ?>

<p style="font-size: 200%; line-height: 100%; width: 500px; text-align: left;"><?php echo $item['title']; ?></p>
<?php if ($GLOBALS['app']['items']['uploads']['enabled'] == TRUE && $item['image'] != NULL) { ?>
	<a href="<?php echo $GLOBALS['app']['items']['uploads']['directory']; ?>/originals/<?php echo $item['image']; ?>"><img src="<?php echo $GLOBALS['app']['items']['uploads']['directory']; ?>/stream/<?php echo $item['image']; ?>" /></a>
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
if ($GLOBALS['app']['private'] != TRUE)
	include 'themes/'.$GLOBALS['app']['theme'].'/items_share.php';
?>

<?php

if ($GLOBALS['app']['items']['likes'] == TRUE)
	include 'themes/'.$GLOBALS['app']['theme'].'/likes_show.php';

if ($GLOBALS['app']['items']['comments'] == TRUE) {
	include 'themes/'.$GLOBALS['app']['theme'].'/comments_list.php';
	$comments_add_show = TRUE;
	include 'themes/'.$GLOBALS['app']['theme'].'/comments_add.php';
}

?>