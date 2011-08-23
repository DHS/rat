<?php if (is_array($app->page->items)) {
foreach ($app->page->items as $item) {
	
	// Prepare vars for comment and like views to be loaded in due course
	$app->page->item = $item;
	
	// Populate some vars
	if ($app->config->items['titles']['enabled'] == TRUE && $item['title'] != NULL) {
		$content = '<h4>'.$this->link_to($item['title'], 'items', 'show', $item['id']).' <small>by '.$this->link_to($item['user']['username'], 'users', 'show', $item['user']['id']).'</small></h4>';
		$content .= '<p>'.$item['content'].'</p>';
	} else {
		$content = '<p>'.$this->link_to($item['user']['username'], 'users', 'show', $item['user']['id']).' '.$item['content'].'</p>';
	}
	
	// Comment form toggle
	if (count($item['comments']) > 0) {
		$app->page->show_comment_form = TRUE;
	} else {
		$app->page->show_comment_form = FALSE;
	}
	
?>

  <!-- Begin item -->

  <!-- Content -->
  <div class="row">
    <div class="span8 columns offset4">
      <?php echo $content; ?>
    </div>
  </div>

  <!-- Meta -->
  <div class="row">
    <div class="span8 columns offset4" style="padding: 5px 0px;">
      <?php echo $app->loadView('items/meta'); ?>
    </div>
  </div>

<?php if ($app->config->items['likes']['enabled'] == TRUE) { ?>
  <!-- Likes -->
  <div class="row">
    <div class="span8 columns offset4" style="padding: 4px 0px;">
      <?php $app->loadView('likes/index'); ?>
    </div>
  </div>
<?php } ?>

<?php if ($app->config->items['comments']['enabled'] == TRUE) { ?>
  <!-- Comments -->
  <div class="row">
    <div class="span8 columns offset4">
    <?php echo $app->loadView('comments/index'); ?>
    </div>
  </div>
<?php } ?>

  <!-- Spacer -->
  <div class="row">
    <div class="span16 columns">
      <p>&nbsp;</p>
    </div>
  </div>

  <!-- End item -->

<?php
unset($app->page->item);
} // end foreach loop
?>

<!--<div class="row">
  <div class="span8 columns offset4">
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
  </div>
</div>-->

<?php
} // end if is_array
?>