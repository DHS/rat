
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

<!--
    <div class="pagination">
      <ul>
        <li class="prev disabled"><a href="#">&larr; Previous</a></li>
        <li class="active"><a href="#">1</a></li>
        <li><a href="#">2</a></li>
        <li><a href="#">3</a></li>
        <li><a href="#">4</a></li>
        <li><a href="#">5</a></li>
        <li class="next"><a href="#">Next &rarr;</a></li>
      </ul>
    </div>
-->

<?php $this->loadPartial('items_add'); ?>

  </div>
</div>

<?php
} // end if is_array
?>
