<?php $this->viewer = User::get_by_id($_SESSION['user_id']); ?>

<?php if ($this->user->id != $_SESSION['user_id']) { ?>

  <span id="friends_<?php echo $this->user->id; ?>">

  <?php if ( ! isset($_SESSION['user_id'])) { ?>

	<a href="<?php $this->url_for('sessions', 'add'); ?>/?redirect_to=/<?php echo $this->url_for('users', 'show', $this->user->id); ?>" class="btn">
	<?php if ($this->config->friends['asymmetric'] == TRUE) { echo 'Follow'; } else { echo 'Add friend'; } ?>
	</a>

  <?php } else { ?>

    <?php if ($this->user->friend_check($_SESSION['user_id']) == TRUE) { ?>

      <a href="#" class="btn" onclick="friend_remove('<?php echo BASE_DIR; ?>', <?php echo $this->user->id; ?>); return false;">
      <?php if ($this->config->friends['asymmetric'] == TRUE) { echo 'Unfollow'; } else { echo 'Remove friend'; } ?>
      </a>

    <?php } else { ?>

      <a href="#" class="btn" onclick="friend_add('<?php echo BASE_DIR; ?>', <?php echo $this->user->id; ?>); return false;">
      <?php if ($this->config->friends['asymmetric'] == TRUE) { echo 'Follow'; } else { echo 'Add friend'; } ?>
      </a>

    <?php } ?>

  <?php } ?>

  </span>

<?php } ?>