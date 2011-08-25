
<div class="row">
  <div class="span8 columns offset4">
    <form action="<?php echo $this->link_to(NULL, 'session', 'add'); if(isset($_GET['redirect_to'])) { echo '/?redirect_to='.$_GET['redirect_to']; } ?>" method="post">
      <fieldset>
        <div class="clearfix">
          <label for="email">Email</label>
          <div class="input">
            <input class="medium" name="email" size="30" type="text" value="<?php echo $email; ?>" />
          </div>
        </div> <!-- /clearfix -->
        <div class="clearfix">
          <label for="password">Password</label>
          <div class="input">
            <input class="medium" name="password" size="30" type="password" />
          </div>
        </div> <!-- /clearfix -->
        <div class="actions">
          <button type="submit" class="btn primary">Login</button>
        </div>
      </fieldset>
    </form>
  </div>
</div>

<div class="row">
  <div class="span3 columns offset6">
    <p class="small">New to <?php echo $this->config->name ?>? <a href="<?php echo $this->link_to(NULL, 'users', 'add'); if(isset($_GET['redirect_to'])) { echo '/?redirect_to='.$_GET['redirect_to']; } ?>">Signup</a> now!
    <br /><?php echo $this->link_to('Forgotten your password', 'users', 'reset'); ?>?</p>
  </div>
</div>
