<?php if ($this->user->id != $_SESSION['user']['id']) { ?>

  <span id="friends_<?php echo $this->user->id; ?>">
  
  <?php if (empty($_SESSION['user'])) { ?>

    <a href="<?php $this->link_to(NULL, 'sessions', 'add'); ?>/?redirect_to=/<?php echo $this->link_to(NULL, 'users', 'show', $this->user->id); ?>" class="btn"><?php if ($this->config->friends['asymmetric'] == TRUE) { echo 'Follow'; } else { echo 'Add friend'; } ?></a>

  <?php } else { ?>
  
    <?php if (Friend::check($_SESSION['user']['id'], $this->user->id) == TRUE) { ?>

      <a href="#" class="btn" onclick="friend_remove(<?php echo $_SESSION['user']['id']; ?>, <?php echo $this->user->id; ?>); return false;">
      <?php if ($this->config->friends['asymmetric'] == TRUE) { echo 'Unfollow'; } else { echo 'Remove friend'; } ?>
      </a>

    <?php } else { ?>

      <a href="#" class="btn" onclick="friend_add(<?php echo $_SESSION['user']['id']; ?>, <?php echo $this->user->id; ?>); return false;">
      <?php if ($this->config->friends['asymmetric'] == TRUE) { echo 'Follow'; } else { echo 'Add friend'; } ?>
      </a>
  
    <?php } ?>

  <?php } ?>
  
  </span>

<?php } ?>