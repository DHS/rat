
<?php 

if (!empty($_SESSION['user'])) { ?>

<div class="center_container">

<h2>New <?php echo $app['items']['name']; ?></h2>

<form action="item.php" method="post">

	<table class="center">
		<?php
		//var_dump($GLOBALS['app']['items']);
		if ($GLOBALS['app']['items']['titles'] == TRUE)
			echo '<tr><td colspan="2">Title: <input type="text" name="title" size="50" value="'.$_GET['title'].'" /></td></tr>';
		?>
		<tr><td class="align_right"></td><td class="align_left"><textarea name="content" rows="5" cols="50"><?php echo $_GET['content']; ?></textarea></td></tr>
		<tr><td></td><td class="align_left"><input type="submit" value="Submit" /></td></tr>
	</table>
	
</form>

</div>

<p>&nbsp;</p>

<?php } ?>
