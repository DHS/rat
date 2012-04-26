
<div class="row">
  <div class="span8 columns offset3">

	<?php

	if (count($this->items) > 0) {

		if (is_array($this->items)) {

			foreach ($this->items as $this->item) {
				$this->loadPartial('item');
			}

			$this->loadPartial('pagination');

		}

	} else {

		echo '<p>No ' . $this->config->items['name_plural'].' found.</p>';

	}

	?>

  </div>
  <div class="span4 columns">

<?php

// Show follow button
if ($this->config->friends['enabled'] == TRUE) {
	$this->loadPartial('friend');
}

// User pofile
if (isset($this->user->full_name) || isset($this->user->bio) || isset($this->user->url)) { ?>

  <h3>Profile</h3>

  <p>
  <?php if (isset($this->user->full_name)) { ?>
    <strong>Name</strong> <?php echo $this->user->full_name; ?>
  <?php } if (isset($this->user->bio)) { ?>
    <br /><strong>Bio</strong> <?php echo $this->user->bio; ?>
  <?php } if (isset($this->user->url)) { ?>
    <br /><strong>URL</strong> <a href="<?php echo $this->user->url; ?>"><?php echo $this->user->url; ?></a>
  <?php } ?>
  <p />

<?php

}

// Show number of points
if (isset($this->plugins->points)) {
	$this->plugins->points->view();
}

?>

  </div>
</div>
