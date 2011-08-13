<?php

if (is_array($items)) {
	
echo '<table style="width: 100%;">';

foreach ($items as $item) {

?>

	<tr>
		<td>
		
			<?php if ($app->items['titles']['enabled'] == TRUE) { ?>
			<h2><?php echo $item['title']; ?></h2>
			<?php } ?>
			
			<p><?php echo $item['content']; ?></p>

			<?php $app->loadView('items_meta'); ?>

			<?php

			if ($app->items['likes']['enabled'] == TRUE)
				$app->loadView('likes_list');
			
			if ($app->items['comments']['enabled'] == TRUE) {

				$app->loadView('comments_list');
				if (count($item['comments']) > 0) {
					$show_comment_form = TRUE;
				} else {
					$show_comment_form = FALSE;
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

}
// end foreach loop

echo '</table>';

}
// end if is_array

?>