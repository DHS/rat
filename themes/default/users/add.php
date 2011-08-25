
<div class="row">
  <div class="span8 columns offset4">
    <form action="<?php echo $this->link_to(NULL, 'users', 'add'); if (isset($_GET['redirect_to'])) { echo '/?redirect_to='.$_GET['redirect_to']; } ?>" method="post">
      <fieldset>
        <?php if (isset($this->code)) { echo '<input type="hidden" name="code" value="'.$this->code.'" />'; } ?>
        <div class="clearfix">
          <label for="email">Email</label>
          <div class="input">
            <input class="medium" name="email" size="30" type="text" value="<?php if (isset($_GET['email'])) { echo $_GET['email']; } ?>" />
          </div>
        </div> <!-- /clearfix -->
        <div class="clearfix">
          <label for="email">Username</label>
          <div class="input">
            <input class="medium" name="username" size="30" type="text" value="<?php if (isset($_GET['username'])) { echo $_GET['username']; } ?>" />
          </div>
        </div> <!-- /clearfix -->
        <div class="clearfix">
          <label for="email">Password</label>
          <div class="input">
            <input class="medium" name="password1" size="30" type="password" value="" />
          </div>
        </div> <!-- /clearfix -->
        <div class="clearfix">
          <label for="email">Confirm password</label>
          <div class="input">
            <input class="medium" name="password2" size="30" type="password" value="" />
          </div>
        </div> <!-- /clearfix -->
        <div class="actions">
          <button type="submit" class="btn primary">Signup</button>
        </div>
      </fieldset>
    </form>
    <p class="small">Already got a <?php echo $this->config->name ?> account? <a href="<?php echo $this->link_to(NULL, 'sessions', 'add'); if (isset($_GET['redirect_to'])) { echo '/?redirect_to='.$_GET['redirect_to']; } ?>">Login</a> now!</p>
  </div>
</div>
