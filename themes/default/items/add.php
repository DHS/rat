
<?php 

if (!empty($_SESSION['user'])) { ?>

<h2>New <?php echo $this->config->items['name']; ?></h2>

<form action="<?php echo $this->link_to(NULL, 'items', 'add'); ?>" method="post" enctype="multipart/form-data">

	<table>
		<?php
		if ($this->config->items['titles']['enabled'] == TRUE)
			echo '<tr><td class="align_right">'.$this->config->items['titles']['name'].': </td><td class="alight_left"><input type="text" name="title" size="50" value="'; if (isset($_GET['title'])) echo $_GET['title']; echo '" /></td></tr>';
		?>
		<tr><td class="align_right"><?php echo $this->config->items['content']['name']; ?>: </td><td class="align_left"><textarea name="content" rows="5" cols="50"><?php if (isset($_GET['content'])) echo $_GET['content']; ?></textarea></td></tr>
		<?php
		if ($this->config->items['uploads']['enabled'] == TRUE)
			echo '<tr><td class="align_right"><label for="file">'.$this->config->items['uploads']['name'].':</label></td><td class="alight_left"><input type="file" name="file" id="file" /></td></tr>';
		?>
		<tr><td></td><td class="align_left"><input type="submit" value="Submit" /></td></tr>
	</table>
	
</form>

<p>&nbsp;</p>

<?php } ?>
