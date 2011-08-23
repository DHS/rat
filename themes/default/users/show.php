
<div class="row">
  <div class="span4 columns offset12">

<?php if ($app->page->user['full_name'] != NULL || $app->page->user['bio'] != NULL || $app->page->user['url'] != NULL) { // Only show profile if there is some data ?>

<h3>Profile</h3>

<?php if ($app->page->user['full_name'] != NULL) { ?>
      <strong>Name</strong> <?php echo $app->page->user['full_name']; ?>
<?php } if ($app->page->user['bio'] != NULL) { ?>
      <strong>Bio</strong> <?php echo $app->page->user['bio']; ?>
<?php } if ($app->page->user['url'] != NULL) { ?>
      <strong>URL</strong> <a href="<?php echo $app->page->user['url']; ?>" target="_new"><?php echo $app->page->user['url']; ?></a>
<?php } ?>

<p />

<?php
}

// Show follow button

if ($app->config->friends['enabled'] == TRUE)
	$app->loadView('friends/index');

// Show number of points

if (isset($app->plugins->points))
	$app->plugins->points->view();

// Show new item form

if ($_SESSION['user']['post_permission'] == 1) {
	$app->loadView('items/add');
}

?>

  </div>
</div>

<?php
// List all items for this user

if (count($app->page->items) > 0) {

	$app->loadView('items/user');

} else {

	// If own page and no post_permission OR someone else's page show 'no articles yet'
	if (($_SESSION['user']['id'] == $app->page->user['id'] && $_SESSION['user']['post_permission'] == 0) || $_SESSION['user']['id'] != $app->page->user['id'])
		echo '<p>'.$app->page->user['username'].' hasn\'t published any '.$app->config->items['name_plural'].' yet.</p>';

}

?>