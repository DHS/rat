<h2>Search <?php echo $this->config->items['name_plural']; ?></h2>

<form action="<?php echo $this->url_for('search'); ?>" method="get">
	<input type="text" name="q" id="q" size="50" value="<?php if (isset($this->uri['params']['q'])) { echo $this->uri['params']['q']; } ?>" /> <input type="submit" value="Search" class="btn" />
</form>