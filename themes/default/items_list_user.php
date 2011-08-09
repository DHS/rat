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

			<?php include 'items_meta.php'; ?>

			<?php

			if ($app->items['likes']['enabled'] == TRUE)
				include 'themes/'.$app->theme.'/likes_list.php';
			
			if ($app->items['comments']['enabled'] == TRUE) {

				include 'themes/'.$app->theme.'/comments_list.php';
				if (count($item['comments']) > 0) {
					$show_comment_form = TRUE;
				} else {
					$show_comment_form = FALSE;
				}
				include 'themes/'.$app->theme.'/comments_add.php';
				
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