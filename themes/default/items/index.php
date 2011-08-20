<?php

if (is_array($app->page->items)) {
	
echo '<table style="width: 100%;">';

foreach ($app->page->items as $item) {

	$app->page->item = $item;

?>

	<tr>
		<?php 
		if (isset($app->plugins->gravatar)) {
			echo '<td style="text-align: center;" valign="top">';
			echo $app->plugins->gravatar->show($item['user']['email'], array('link' => "/{$item['user']['username']}", 'size' => 48, 'style' => "margin-right: 5px;"));
			echo '</td>';
		}
		?>
		<td style="padding-bottom: 10px;">
		
			<?php if ($app->config->items['titles']['enabled'] == TRUE) { ?>
				
				<p><a href="/<?php echo $item['user']['username']; ?>/<?php echo $app->config->items['name']; ?>/<?php echo $item['id']; ?>"><?php echo $item['title']; ?></a> by <a href="/<?php echo $item['user']['username']; ?>"><?php echo $item['user']['username']; ?></a></p>
				<?php if ($app->config->items['uploads']['enabled'] == TRUE && $item['image'] != NULL) { ?>
					<a href="/<?php echo $item['user']['username']; ?>/item/<?php echo $item['id']; ?>"><img src="<?php echo $app->config->items['uploads']['directory']; ?>/stream/<?php echo $item['image']; ?>" /></a>
				<?php } ?>
				<p><?php echo $item['content']; ?></p>

			<?php } else { ?>
				
				<p><a href="/<?php echo $item['user']['username']; ?>"><?php echo $item['user']['username']; ?></a> <?php echo $item['content']; ?></p>
			
			<?php } ?>

			<?php $app->loadView('items/meta'); ?>

			<?php
			
			if ($app->config->items['likes']['enabled'] == TRUE)
				$app->loadView('likes');
			
			if ($app->config->items['comments']['enabled'] == TRUE) {

				if (count($item['comments']) > 0) {
					$app->page->show_comment_form = TRUE;
				} else {
					$app->page->show_comment_form = FALSE;
				}
				$app->loadView('comments');

			}

			?>
			
		</td>
	</tr>
	
	<tr>
		<?php 
		if (isset($app->plugins->gravatar)) {
			echo '<td colspan="2" style="border-top: 1px solid #CCCCCC; height: 10px;"></td>';
		} else {
			echo '<td colspan="2" style="border-top: 1px solid #CCCCCC; height: 10px;"></td>';
		}
		?>
	</tr>
	
<?php

	unset($app->page->item);

}
// end foreach loop

echo '</table>';

}
// end if is_array

?>