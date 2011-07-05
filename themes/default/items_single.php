
<?php if ($GLOBALS['app']['items']['titles']['enabled'] == TRUE) { ?>

<p style="font-size: 200%; line-height: 100%; width: 500px; text-align: left;"><?php echo $item['title']; ?></p>
<p><?php echo $item['content'] ?></p>

<?php } else { ?>

<p style="font-size: 200%; line-height: 100%; width: 500px; text-align: left;"><?php echo $item['content']; ?></p>  

<?php } ?>

<p style="font-size: 50%; width: 500px; text-align: left;">

	<?php
	if (is_object($GLOBALS['gravatar']))
		echo $GLOBALS['gravatar']->show($item['user']['email'], array('user_id' => $item['user']['id'], 'size' => 20, 'style' => "margin-bottom: -7px;"));
	?>
	<a href="user.php?id=<?php echo $item['user']['id']; ?>"><?php echo $item['user']['username']; ?></a>

	&middot; <?php include 'items_meta.php'; ?>
	
</p>

<?php if ($GLOBALS['app']['private'] != TRUE) { ?>

<p style="width: 500px; text-align: right;">
	
	<span style="float: right; margin-left: 20px;">

		<a href="http://twitter.com/share" class="twitter-share-button" data-count="none">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
		
		<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo $GLOBALS['app']['url'].substr($_SERVER['REQUEST_URI'], 1); ?>&amp;layout=button_count&amp;show_faces=true&amp;width=100&amp;action=like&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>

	</span>
	
</p>

<?php } ?>

<?php

if ($GLOBALS['app']['items']['likes'] == TRUE)
	include 'themes/'.$GLOBALS['app']['theme'].'/likes_show.php';

if ($GLOBALS['app']['items']['comments'] == TRUE) {
	include 'themes/'.$GLOBALS['app']['theme'].'/comments_show.php';
	$comments_add_show = TRUE;
	include 'themes/'.$GLOBALS['app']['theme'].'/comments_add.php';
}

?>