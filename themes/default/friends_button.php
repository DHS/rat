<?php if ($app->page->user['id'] != $_SESSION['user']['id']) { ?>
<span id="friends_<?php echo $app->page->user['id']; ?>">
<?php if (empty($_SESSION['user'])) { ?>
<a href="login.php?redirect_to=/<?php echo $app->page->user['username']; ?>" class="friend"><?php if ($app->config->friends['asymmetric'] == TRUE) { echo 'Follow'; } else { echo 'Add friend'; } ?></a>
<?php } else {

	if ($app->friend->check($_SESSION['user']['id'], $app->page->user['id']) == TRUE) {
		$app->loadView('friends_remove');
	} else {
		$app->loadView('friends_add');
	}
	
} ?>
</span>
<?php } ?>