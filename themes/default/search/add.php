
<h2>Search <?php echo $app->config->items['name_plural']; ?></h2>

<form action="/search/" method="get">
	<input type="text" name="q" id="q" size="50" value="<?php if (isset($_GET['q'])) echo $_GET['q']; ?>" /> <input type="submit" value="Search" class="btn" />
</form>

<p>&nbsp;</p>
