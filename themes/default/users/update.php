
<div class="row">
  <div class="span8 columns offset4">

    <ul class="tabs">
      <li<?php if ($this->page == 'profile') { echo ' class="active"'; } ?>><?php echo $this->link_to('Profile', 'users', 'update', 'profile'); ?></li>
      <li<?php if ($this->page == 'password') { echo ' class="active"'; } ?>><?php echo $this->link_to('Password', 'users', 'update', 'password'); ?></li>
    </ul>

<?php if ($this->page == 'password') { ?>
	
	<form action="<?php echo $this->url_for('users', 'update', 'password'); ?>" method="post">
      <fieldset>
        <legend>Change password</legend>
        <div class="clearfix">
          <label for="old_password">Old password</label>
          <div class="input">
            <input class="medium" name="old_password" size="30" type="password" />
          </div>
        </div> <!-- /clearfix -->
        <div class="clearfix">
          <label for="new_password1">New password</label>
          <div class="input">
            <input class="medium" name="new_password1" size="30" type="password" />
          </div>
        </div> <!-- /clearfix -->
        <div class="clearfix">
          <label for="new_password2">New password again</label>
          <div class="input">
            <input class="medium" name="new_password2" size="30" type="password" />
          </div>
        </div> <!-- /clearfix -->
        <div class="actions">
          <button type="submit" class="btn primary">Save</button>
        </div>
      </fieldset>
    </form>

<?php } elseif ($this->page == 'profile') { ?>

    <form action="<?php echo $this->url_for('users', 'update', 'profile'); ?>" method="post">
      <fieldset>
        <legend>Update profile</legend>
        <div class="clearfix">
          <label for="name">Full name</label>
          <div class="input">
            <input class="medium" name="full_name" size="30" type="text" value="<?php if (isset($this->user->full_name) { echo $this->user->full_name; } ?>" />
          </div>
        </div> <!-- /clearfix -->
        <div class="clearfix">
          <label for="bio">Bio</label>
          <div class="input">
            <input class="medium" name="bio" size="30" type="text" value="<?php if (isset($this->user->bio)) { echo $this->user->bio; } ?>" />
          </div>
        </div> <!-- /clearfix -->
        <div class="clearfix">
          <label for="url">URL</label>
          <div class="input">
            <input class="medium" name="url" size="30" type="text" value="<?php if (isset($this->user->url)) { echo $this->user->url; } else { echo 'http://'; } ?>" />
          </div>
        </div> <!-- /clearfix -->
        <div class="actions">
          <button type="submit" class="btn primary">Save</button>
        </div>
      </fieldset>
    </form>

<?php } ?>

  </div>
</div>
