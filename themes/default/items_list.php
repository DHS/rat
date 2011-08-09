<?php

if (is_array($items)) {
	
echo '<table style="width: 500px;">';

foreach ($items as $item) { ?>

	<tr>
		<?php 
		if (is_object($GLOBALS['gravatar'])) {
			echo '<td style="text-align: center;" valign="top">';
			echo $GLOBALS['gravatar']->show($item['user']['email'], array('user_id' => $item['user']['id'], 'size' => 48, 'style' => "margin-right: 5px;"));
			echo '</td>';
		}
		?>
		<td style="padding-bottom: 10px;">
		
			<?php if ($app->items['titles']['enabled'] == TRUE) { ?>
				
				<p><a href="item.php?id=<?php echo $item['id']; ?>"><?php echo $item['title']; ?></a> by <a href="user.php?id=<?php echo $item['user']['id']; ?>"><?php echo $item['user']['username']; ?></a></p>
				<?php if ($app->items['uploads']['enabled'] == TRUE && $item['image'] != NULL) { ?>
					<a href="item.php?id=<?php echo $item['id']; ?>"><img src="<?php echo $app->items['uploads']['directory']; ?>/stream/<?php echo $item['image']; ?>" /></a>
				<?php } ?>
				<p><?php echo $item['content']; ?></p>

			<?php } else { ?>
				
				<p><a href="user.php?id=<?php echo $item['user']['id']; ?>"><?php echo $item['user']['username']; ?></a> <?php echo $item['content']; ?></p>
			
			<?php } ?>
					
			<p>
			<?php include 'items_meta.php'; ?>
			</p>

			<?php
			
			if ($app->items['likes']['enabled'] == TRUE)
				include 'themes/'.$app->theme.'/likes_list.php';
			
			if ($app->items['comments']['enabled'] == TRUE) {

				include 'themes/'.$app->theme.'/comments_list.php';
				if (count($item['comments']) > 0) {
					$comments_add_show = TRUE;
				} else {
					$comments_add_show = FALSE;
				}
				include 'themes/'.$app->theme.'/comments_add.php';

			}

			?>
			
		</td>
	</tr>
	
	<tr>
		<?php 
		if (is_object($GLOBALS['gravatar'])) {
			echo '<td colspan="2" style="border-top: 1px solid #CCCCCC; height: 10px;"></td>';
		} else {
			echo '<td colspan="2" style="border-top: 1px solid #CCCCCC; height: 10px;"></td>';
		}
		?>
	</tr>
	
<?php

}
// end foreach loop

echo '</table>';

}
// end if is_array

?>