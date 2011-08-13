<?php if ($user['id'] != $_SESSION['user']['id']) { ?>
<span id="friends_<?php echo $user['id']; ?>">
<?php if (empty($_SESSION['user'])) { ?>
<a href="login.php?redirect_to=/user.php?id=<?php echo $user['id']; ?>" class="friend"><?php if ($app->config->friends['asymmetric'] == TRUE) { echo 'Follow'; } else { echo 'Add friend'; } ?></a>
<?php } else {

	if ($app->friend->check($_SESSION['user']['id'], $user['id']) == TRUE) {
		include 'friends_remove.php';
	} else {
		include 'friends_add.php';
	}
	
} ?>
</span>
<?php } ?>