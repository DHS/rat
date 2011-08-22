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
			$gravatar = $app->plugins->gravatar->show($item['user']['email'], array('size' => 48, 'style' => "margin-right: 5px;"));
			echo $this->link_to($gravatar, 'users', 'show', $item['user']['id']).' ';
			echo '</td>';
		}
		?>
		<td style="padding-bottom: 10px;">
		
			<?php if ($app->config->items['titles']['enabled'] == TRUE && $item['title'] != NULL) { ?>
				
				<p><?php echo $this->link_to($item['title'], 'items', 'show', $item['id']); ?> by <?php echo $this->link_to($item['user']['username'], 'users', 'show', $item['user']['id']); ?></p>
				<?php if ($app->config->items['uploads']['enabled'] == TRUE && $item['image'] != NULL) { ?>
					<?php echo $this->link_to('<img src="'.$app->config->items['uploads']['directory'].'/stream/'.$item['image'].'" />', 'items', 'show', $item['id']); ?>
				<?php } ?>
				<p><?php echo $item['content']; ?></p>

			<?php } else { ?>
				
				<p><?php echo $this->link_to($item['user']['username'], 'users', 'show', $item['user']['id']); ?> <?php echo $item['content']; ?></p>
			
			<?php } ?>

			<?php $app->loadView('items/meta'); ?>

			<?php
			
			if ($app->config->items['likes']['enabled'] == TRUE)
				$app->loadView('likes/index');
			
			if ($app->config->items['comments']['enabled'] == TRUE) {

				if (count($item['comments']) > 0) {
					$app->page->show_comment_form = TRUE;
				} else {
					$app->page->show_comment_form = FALSE;
				}
				$app->loadView('comments/index');

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