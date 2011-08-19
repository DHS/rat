<?php

if (is_array($app->page->items)) {
	
echo '<table style="width: 100%;">';

foreach ($app->page->items as $item) {

	$app->page->item = $item;
	
?>

	<tr>
		<td>
		
			<?php if ($app->config->items['titles']['enabled'] == TRUE) { ?>
			<h2><?php echo $item['title']; ?></h2>
			<?php } ?>
			
			<p><?php echo $item['content']; ?></p>

			<?php $app->loadView('items_meta'); ?>

			<?php

			if ($app->config->items['likes']['enabled'] == TRUE)
				$app->loadView('likes');
			
			if ($app->config->items['comments']['enabled'] == TRUE) {

				$app->loadView('comments_list');
				if (count($item['comments']) > 0) {
					$app->page->show_comment_form = TRUE;
				} else {
					$app->page->show_comment_form = FALSE;
				}
				$app->loadView('comments_add');
				
			}

			?>
			
		</td>
	</tr>
	
	<tr>
		<td style="border-top: 1px solid #CCCCCC; height: 10px;"></td>
	</tr>
	
<?php

	unset($app->page->item);

}
// end foreach loop

echo '</table>';

}
// end if is_array

?>