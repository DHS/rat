
<h2>Search <?php echo $app->config->items['name_plural']; ?></h2>

<form action="/search" method="get">
	<input type="text" name="q" id="q" size="50" value="<?php echo $_GET['q']; ?>" /> <input type="submit" value="Search" />
</form>
