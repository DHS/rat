
<?php if (is_array($this->items)) { ?>

<div class="row">
  <div class="span8 columns offset3">

<?php foreach ($this->items as $item) {
	
	// Prepare vars for comment and like views to be loaded in due course
	$this->item = $item;
	
	// Populate some vars
	if (isset($this->plugins->gravatar)) {
		$image = $this->plugins->gravatar->show($item['user']['email'], array('size' => 48, 'style' => 'float: left; padding: 0px 10px 10px 0px;'));
		$gravatar = $this->link_to($image, 'users', 'show', $item['user']['id']).' ';
	} else {
		$gravatar = $this->link_to($like['user']['username'], 'users', 'show', $item['user']['id']).' ';
	}
	
	if ($this->config->items['titles']['enabled'] == TRUE && $item['title'] != NULL) {
		$content = '<h4>'.$this->link_to($item['title'], 'items', 'show', $item['id']).' <small>by '.$this->link_to($item['user']['username'], 'users', 'show', $item['user']['id']).'</small></h4>';
		$content .= '<p>'.$item['content'].'</p>';
	} else {
		$content = '<p>'.$this->link_to($item['user']['username'], 'users', 'show', $item['user']['id']).' '.$item['content'].'</p>';
	}
	
	// Comment form toggle
	if (count($item['comments']) > 0) {
		$this->page->show_comment_form = TRUE;
	} else {
		$this->page->show_comment_form = FALSE;
	}
	
?>

    <!-- Begin item -->
    
    <!-- Content -->
    <?php echo $gravatar; ?>
    <?php echo $content; ?>
    
    <!-- Meta -->
    <?php echo $this->loadView('items/meta'); ?>

<?php if ($this->config->items['likes']['enabled'] == TRUE) { ?>
    <!-- Likes -->
    <?php $this->loadView('likes/index'); ?>
<?php } ?>

<?php if ($this->config->items['comments']['enabled'] == TRUE) { ?>
    <!-- Comments -->
    <?php echo $this->loadView('comments/index'); ?>
<?php } ?>

    <!-- Spacer -->
    <p>&nbsp;</p>

    <!-- End item -->

<?php
unset($this->item);
} // end foreach loop
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

<?php $this->loadView('items/add'); ?>

  </div>
</div>

<?php
} // end if is_array
?>
