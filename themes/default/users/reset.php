

<div class="row">
  <div class="span8 columns offset4">
    <form action="<?php echo $this->link_to(NULL, 'users', 'reset', $this->code); ?>" method="post">
      <fieldset>
        <legend>Set new password</legend>
        <div class="clearfix">
          <label for="password1">Password</label>
          <div class="input">
            <input class="medium" name="password1" size="30" type="password" />
          </div>
        </div> <!-- /clearfix -->
        <div class="clearfix">
          <label for="password2">Password again</label>
          <div class="input">
            <input class="medium" name="password2" size="30" type="password" />
          </div>
        </div> <!-- /clearfix -->
        <div class="actions">
          <button type="submit" class="btn primary">Reset</button>
        </div>
      </fieldset>
    </form>
  </div>
</div>
