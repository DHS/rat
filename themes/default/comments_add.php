
<?php if ($app->config->items['comments']['enabled'] == TRUE && ($app->config->private == TRUE || $_SESSION['user'] != NULL)) { ?>

		<form action="javascript:comment_add(<?php echo $_SESSION['user']['id']; ?>, <?php echo $item['id']; ?>);" id="comment_form_<?php echo $item['id']; ?>" class="meta" style="margin: 0px; <?php if ($show_comment_form != TRUE) { echo 'visibility: hidden; height: 0px;'; }?>" method="post">
			<input type="text" name="content" size="30" value="" /> <input type="submit" value="<?php echo $app->config->items['comments']['name']; ?>" />
		</form>
	
<?php } ?>
