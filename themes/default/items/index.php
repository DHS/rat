<?php

// User not logged in so show explanation sentence
if (!isset($_SESSION['user_id'])) {
	echo '<h1><small>'.$this->config->tagline.'</small></h1>';
}

// App is private and user not logged in so show beta signup form
if ($this->config->private == TRUE && !isset($_SESSION['user_id'])) {
	$this->loadView('users/add', NULL, 'none');
}

// App public or user logged in so show items
if ($this->config->private == FALSE || isset($_SESSION['user_id'])) { ?>

	<?php if (is_array($this->items) && count($this->items) > 0) { ?>

    <h1>Stream</h1>

<div class="row">
  <div class="span8 columns offset3">

	<?php
	foreach ($this->items as $this->item) {
		$this->loadPartial('item');
	}
	?>

	<?php $this->loadPartial('pagination'); ?>

  </div>
  <div class="span4 columns">

	<?php $this->loadPartial('item_add'); ?>

  </div>
</div>

	<?php } else { ?>
		
		<h1>No <?php echo $this->config->items['name_plural']; ?> found...</h1>
		
	<?php } ?>

<?php } ?>
