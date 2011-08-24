
<?php if (is_array($page['items'])) { ?>

<?php foreach ($page['items'] as $item) {
	$page['item'] = $item; ?>

  <div class="row">
    <div class="span2 columns offset4">
      <?php echo $app->plugins->gravatar->show($item['user']['email'], array('size' => 48)); ?>
    </div>
    <div class="span6 columns">
      <?php if ($this->config->items['titles']['enabled'] == TRUE && $item['title'] != NULL) { ?>
      <p><?php echo $this->link_to($item['title'], 'items', 'show', $item['id']); ?> by <?php echo $this->link_to($item['user']['username'], 'users', 'show', $item['user']['id']); ?></p>
      <p><?php echo $item['content']; ?></p>
      <?php } else { ?>
      <p><?php echo $this->link_to($item['user']['username'], 'users', 'show', $item['user']['id']); ?> <?php echo $item['content']; ?></p>
      <?php } ?>
    </div>
  </div>
  <div class="row">
    <div class="span8 columns offset4">
      <?php $this->loadView('items/meta'); ?>
    </div>
  </div>
  <?php if ($this->config->items['likes']['enabled'] == TRUE) ?>
  <div class="row">
    <div class="span8 columns offset4">
      <?php $this->loadView('likes/index'); ?>
    </div>
  </div>
  <?php } ?>
  <div class="row">
    <div class="span8 columns offset4">
		<?php if ($this->config->items['comments']['enabled'] == TRUE) {
			if (count($item['comments']) > 0) {
				$page['show_comment_form'] = TRUE;
			} else {
				$page['show_comment_form'] = FALSE;
			}
			$this->loadView('comments/index');
		} ?>
    </div>
  </div>
  <div class="row">
    <div class="span16 columns">
      &nbsp;
    </div>
  </div>
  <div class="row">
    <div class="span16 columns">
      &nbsp;
    </div>
  </div>

<?php unset($page['item']);
} // end foreach loop ?>

<?php } // end if is_array ?>
