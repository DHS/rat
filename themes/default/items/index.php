
<?php if (is_array($this->items)) { ?>

<div class="row">
  <div class="span8 columns offset3">

<?php

foreach ($this->items as $this->item) {
	$this->loadPartial('item');
}

$this->loadPartial('pagination');

?>

  </div>
  <div class="span4 columns">

    <?php $this->loadPartial('item_add'); ?>

  </div>
</div>

<?php } ?>
