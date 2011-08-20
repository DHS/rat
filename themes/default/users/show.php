
<?php if ($app->page->user['full_name'] != NULL || $app->page->user['bio'] != NULL || $app->page->user['url'] != NULL) { // Only show profile if there is some data ?>

<div class="center_container">
<table class="center">

<?php if ($app->page->user['full_name'] != NULL) { ?>
<tr><td class="align_right"><strong>Name</strong></td><td class="align_left" style="padding-left: 10px;"><?php echo $app->page->user['full_name']; ?></td></tr>
<?php } if ($app->page->user['bio'] != NULL) { ?>
<tr><td class="align_right"><strong>Bio</strong></td><td class="align_left" style="padding-left: 10px;"><?php echo $app->page->user['bio']; ?></td></tr>
<?php } if ($app->page->user['url'] != NULL) { ?>
<tr><td class="align_right"><strong>URL</strong></td><td class="align_left" style="padding-left: 10px;"><a href="<?php echo $app->page->user['url']; ?>" target="_new"><?php echo $app->page->user['url']; ?></a></td></tr>
<?php } ?>

</table>
</div>

<p />

<?php } ?>

<?php

// Show follow button

if ($app->config->friends['enabled'] == TRUE)
	$app->loadView('friends_button');

// Show number of points

if (isset($app->plugins->points))
	$app->plugins->points->view();

// Show new item form

//if ($_SESSION['user']['post_permission'] == 1)
//	$app->loadView('items_add');

// List all items for this user

if (count($app->page->items) > 0) {

	$app->loadView('items/user');

} else {

	// If own page and no post_permission OR someone else's page show 'no articles yet'
	if (($_SESSION['user']['id'] == $app->page->user['id'] && $_SESSION['user']['post_permission'] == 0) || $_SESSION['user']['id'] != $app->page->user['id'])
		echo '<p>'.$app->page->user['username'].' hasn\'t published any '.$app->config->items['name_plural'].' yet.</p>';

}

?>