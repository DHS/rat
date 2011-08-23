
<?php 

if (!empty($_SESSION['user'])) { ?>

<h2>New <?php echo $app->config->items['name']; ?></h2>

<form action="<?php echo $this->link_to(NULL, 'items', 'add'); ?>" method="post" enctype="multipart/form-data">

	<table>
		<?php
		if ($app->config->items['titles']['enabled'] == TRUE) {
			echo '<tr><td class="align_right">'.$app->config->items['titles']['name'].': </td><td class="alight_left"><input type="text" name="title" size="50" value="';
			if (isset($_GET['title'])) echo $_GET['title'];
			echo '" /></td></tr>';
		}
		if ($app->config->items['content']['enabled'] == TRUE) {
			echo '<tr><td class="align_right">'.$app->config->items['content']['name'].': </td><td class="align_left"><textarea name="content" rows="5" cols="50">';
			if (isset($_GET['content'])) echo $_GET['content'];
			echo '</textarea></td></tr>';
		}
		if ($app->config->items['uploads']['enabled'] == TRUE)
			echo '<tr><td class="align_right"><label for="file">'.$app->config->items['uploads']['name'].':</label></td><td class="alight_left"><input type="file" name="file" id="file" /></td></tr>';
		?>
		<tr><td></td><td class="align_left"><input type="submit" value="Submit" class="btn" /></td></tr>
	</table>
	
</form>

<p>&nbsp;</p>

<?php } ?>
