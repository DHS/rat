
<div class="row">
  <div class="span8 columns offset4">

    <ul class="tabs">
      <li class="active"><?php echo $this->link_to('Password', 'users', 'update', 'password'); ?></li>
      <li><?php echo $this->link_to('Profile', 'users', 'update', 'profile'); ?></li>
    </ul>

    <form action="<?php echo $this->link_to(NULL, 'users', 'update', 'password'); ?>" method="post">
      <fieldset>
        <legend>Change password</legend>
        <div class="clearfix">
          <label for="old_password">Old password</label>
          <div class="input">
            <input class="medium" name="old_password" size="30" type="text" />
          </div>
        </div> <!-- /clearfix -->
        <div class="clearfix">
          <label for="new_password1">New password</label>
          <div class="input">
            <input class="medium" name="new_password1" size="30" type="text" />
          </div>
        </div> <!-- /clearfix -->
        <div class="clearfix">
          <label for="new_password2">New password again</label>
          <div class="input">
            <input class="medium" name="new_password2" size="30" type="text" />
          </div>
        </div> <!-- /clearfix -->
        <div class="actions">
          <button type="submit" class="btn primary">Save</button>
        </div>
      </fieldset>
    </form>

  </div>
</div>
