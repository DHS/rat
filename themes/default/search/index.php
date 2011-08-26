
<div class="row">
  <div class="span6 columns">

    <?php $this->loadPartial('search'); ?>

  </div>
  <div class="span8 columns offset1">

<?php if (is_array($this->items)) {
	
	foreach ($this->items as $this->item) {
		$this->loadPartial('item');
	}
	
	$this->loadPartial('pagination');
	
} ?>

  </div>
</div>
