
<div class="row">
  <div class="span8 columns offset4">

    <ul class="tabs">
      <li><?php echo $this->link_to('Password', 'users', 'update', 'password'); ?></li>
      <li class="active"><?php echo $this->link_to('Profile', 'users', 'update', 'profile'); ?></li>
    </ul>

    <form action="<?php echo $this->link_to(NULL, 'users', 'update', 'profile'); ?>" method="post">
      <fieldset>
        <legend>Update profile</legend>
        <div class="clearfix">
          <label for="name">Full name</label>
          <div class="input">
            <input class="medium" name="full_name" size="30" type="text" value="<?php echo $_SESSION['user']['full_name']; ?>" />
          </div>
        </div> <!-- /clearfix -->
        <div class="clearfix">
          <label for="bio">Bio</label>
          <div class="input">
            <input class="medium" name="bio" size="30" type="text" value="<?php echo $_SESSION['user']['bio']; ?>" />
          </div>
        </div> <!-- /clearfix -->
        <div class="clearfix">
          <label for="url">URL</label>
          <div class="input">
            <input class="medium" name="url" size="30" type="text" value="<?php if (isset($_SESSION['user']['url'])) { echo $_SESSION['user']['url']; } else { echo 'http://'; } ?>" />
          </div>
        </div> <!-- /clearfix -->
        <div class="actions">
          <button type="submit" class="btn primary">Save</button>
        </div>
      </fieldset>
    </form>

  </div>
</div>
