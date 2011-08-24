
<?php if ($page['user']['full_name'] != NULL || $page['user']['bio'] != NULL || $page['user']['url'] != NULL) { // Only show profile if there is some data ?>

<div class="center_container">
<table class="center">

<?php if ($page['user']['full_name'] != NULL) { ?>
<tr><td class="align_right"><strong>Name</strong></td><td class="align_left" style="padding-left: 10px;"><?php echo $page['user']['full_name']; ?></td></tr>
<?php } if ($page['user']['bio'] != NULL) { ?>
<tr><td class="align_right"><strong>Bio</strong></td><td class="align_left" style="padding-left: 10px;"><?php echo $page['user']['bio']; ?></td></tr>
<?php } if ($page['user']['url'] != NULL) { ?>
<tr><td class="align_right"><strong>URL</strong></td><td class="align_left" style="padding-left: 10px;"><a href="<?php echo $page['user']['url']; ?>" target="_new"><?php echo $page['user']['url']; ?></a></td></tr>
<?php } ?>

</table>
</div>

<p />

<?php } ?>

<?php

// Show follow button

if ($this->config->friends['enabled'] == TRUE)
	$this->loadView('friends/index');

// Show number of points

if (isset($app->plugins->points))
	$app->plugins->points->view();

// Show new item form

//if ($_SESSION['user']['post_permission'] == 1)
//	$this->loadView('items/add');

// List all items for this user

if (count($page['items']) > 0) {

	$this->loadView('items/user');

} else {

	// If own page and no post_permission OR someone else's page show 'no articles yet'
	if (($_SESSION['user']['id'] == $page['user']['id'] && $_SESSION['user']['post_permission'] == 0) || $_SESSION['user']['id'] != $page['user']['id'])
		echo '<p>'.$page['user']['username'].' hasn\'t published any '.$this->config->items['name_plural'].' yet.</p>';

}

?>