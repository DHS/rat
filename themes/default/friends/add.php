<a href="#" class="friend" onclick="friend_add(<?php echo $_SESSION['user']['id']; ?>, <?php echo $this->user->id; ?>); return false;">
<?php if ($this->config->friends['asymmetric'] == TRUE) {
	echo 'Follow';
} else {
	echo 'Add friend';
} ?>
</a>