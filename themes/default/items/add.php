
<?php 

if (!empty($_SESSION['user'])) { ?>

<h2>New <?php echo $app->config->items['name']; ?></h2>

<form action="/<?php echo $_SESSION['user']['username']; ?>/<?php echo $app->config->items['name']; ?>/add" method="post" enctype="multipart/form-data">

	<table>
		<?php
		if ($app->config->items['titles']['enabled'] == TRUE)
			echo '<tr><td class="align_right">'.$app->config->items['titles']['name'].': </td><td class="alight_left"><input type="text" name="title" size="50" value="'; if (isset($_GET['title'])) echo $_GET['title']; echo '" /></td></tr>';
		?>
		<tr><td class="align_right"><?php echo $app->config->items['content']['name']; ?>: </td><td class="align_left"><textarea name="content" rows="5" cols="50"><?php if (isset($_GET['content'])) echo $_GET['content']; ?></textarea></td></tr>
		<?php
		if ($app->config->items['uploads']['enabled'] == TRUE)
			echo '<tr><td class="align_right"><label for="file">'.$app->config->items['uploads']['name'].':</label></td><td class="alight_left"><input type="file" name="file" id="file" /></td></tr>';
		?>
		<tr><td></td><td class="align_left"><input type="submit" value="Submit" /></td></tr>
	</table>
	
</form>

<p>&nbsp;</p>

<?php } ?>
