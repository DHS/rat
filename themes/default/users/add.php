
<?php if ((isset($this->code) && $this->code != '') || $this->config->beta != TRUE) { ?>
<div class="row">
  <div class="span8 columns offset4">
    <form action="<?php if (isset($this->code)) { echo $this->url_for('users', 'add', NULL, array('code' => $this->code)); } else { echo $this->url_for('users', 'add', NULL); } if (isset($this->uri['params']['redirect_to'])) { echo '/?redirect_to=' . $this->uri['params']['redirect_to']; } ?>" method="post">
      <fieldset>
        <legend>Signup</legend>
        <?php if (isset($this->code)) { echo '<input type="hidden" name="code" value="' . $this->code . '" />'; } ?>
        <div class="clearfix">
          <label for="email">Email</label>
          <div class="input">
            <input class="medium" name="email" size="30" type="text" value="<?php if (isset($this->uri['params']['email'])) { echo $this->uri['params']['email']; } ?>" />
          </div>
        </div> <!-- /clearfix -->
        <div class="clearfix">
          <label for="email">Username</label>
          <div class="input">
            <input class="medium" name="username" size="30" type="text" value="<?php if (isset($this->uri['params']['username'])) { echo $this->uri['params']['username']; } ?>" />
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
  </div>
</div>

<div class="row">
  <div class="span8 columns offset4 align_center">
    <p class="small">Already got a <?php echo $this->config->name ?> account? <a href="<?php echo $this->url_for('sessions', 'add'); if (isset($this->uri['params']['redirect_to'])) { echo '/?redirect_to=' . $this->uri['params']['redirect_to']; } ?>">Login</a> now!</p>
  </div>
</div>

<?php } else { ?>

<div class="row">
  <div class="span8 columns offset4">
    <form action="<?php echo $this->url_for('users', 'add'); if (isset($this->uri['params']['redirect_to'])) { echo '/?redirect_to=' . $this->uri['params']['redirect_to']; } ?>" method="post">
      <fieldset>
        <legend>Signup for our beta</legend>
        <?php if (isset($this->uri['params']['code'])) { echo '<input type="hidden" name="code" value="' . $this->uri['params']['code'].'" />'; } ?>
        <?php if (isset($this->uri['params']['email'])) { echo '<input type="hidden" name="email" value="' . $this->uri['params']['email'].'" />'; } ?>
        <div class="clearfix">
          <label for="email">Email</label>
          <div class="input">
            <input class="medium" name="email" size="30" type="text" value="<?php if (isset($this->uri['params']['email'])) { echo $this->uri['params']['email']; } ?>" />
          </div>
        </div> <!-- /clearfix -->
        <div class="actions">
          <button type="submit" class="btn primary">Win</button>
        </div>
      </fieldset>
    </form>
  </div>
</div>

<?php } ?>
