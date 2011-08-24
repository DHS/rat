<a href="#" class="friend" onclick="friend_remove(<?php echo $_SESSION['user']['id']; ?>, <?php echo $page['user']['id']; ?>); return false;">
<?php if ($this->config->friends['asymmetric'] == TRUE) {
	echo 'Unfollow';
} else {
	echo 'Remove friend';
} ?>
</a>