<?php

if ($this->user->id == $_SESSION['user']['id']) {
	$this->loadView('items/add');
}

if (is_array($this->items)) {
	
echo '<table style="width: 100%;">';

foreach ($this->items as $item) {

	$page['item'] = $item;
	
?>

	<tr>
		<td>
		
			<?php if ($this->config->items['titles']['enabled'] == TRUE) { ?>
			<h2><?php echo $item->title; ?></h2>
			<?php } ?>
			
			<p><?php echo $item->content; ?></p>

			<?php $this->loadView('items/meta'); ?>

			<?php

			if ($this->config->items['likes']['enabled'] == TRUE)
				$this->loadView('likes/index');
			
			if ($this->config->items['comments']['enabled'] == TRUE) {

				if (count($item->comments) > 0) {
					$page['show_comment_form'] = TRUE;
				} else {
					$page['show_comment_form'] = FALSE;
				}
				$this->loadView('comments/index');
				
			}

			?>
			
		</td>
	</tr>
	
	<tr>
		<td style="border-top: 1px solid #CCCCCC; height: 10px;"></td>
	</tr>
	
<?php

	unset($page['item']);

}
// end foreach loop

echo '</table>';

}
// end if is_array

?>