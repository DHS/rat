
<?php 

if (!empty($_SESSION['user'])) { ?>

<h2>New <?php echo $app->items['name']; ?></h2>

<form action="item.php" method="post" enctype="multipart/form-data">

	<table>
		<?php
		if ($app->items['titles']['enabled'] == TRUE)
			echo '<tr><td class="align_right">'.$app->items['titles']['name'].': </td><td class="alight_left"><input type="text" name="title" size="50" value="'.$_GET['title'].'" /></td></tr>';
		?>
		<tr><td class="align_right"><?php echo $app->items['content']['name']; ?>: </td><td class="align_left"><textarea name="content" rows="5" cols="50"><?php echo $_GET['content']; ?></textarea></td></tr>
		<?php
		if ($app->items['uploads']['enabled'] == TRUE)
			echo '<tr><td class="align_right"><label for="file">'.$app->items['uploads']['name'].':</label></td><td class="alight_left"><input type="file" name="file" id="file" /></td></tr>';
		?>
		<tr><td></td><td class="align_left"><input type="submit" value="Submit" /></td></tr>
	</table>
	
</form>

<p>&nbsp;</p>

<?php } ?>
