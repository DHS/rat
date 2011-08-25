
<div class="row">
  <div class="span8 columns offset4">
    <form action="<?php echo $this->link_to(NULL, 'users', 'reset'); ?>" method="post">
      <fieldset>
        <legend>Reset password</legend>
        <div class="clearfix">
          <label for="email">Email</label>
          <div class="input">
            <input class="medium" name="email" size="30" type="text" value="<?php echo $_GET['email']; ?>" />
          </div>
        </div> <!-- /clearfix -->
        <div class="actions">
          <button type="submit" class="btn primary">Reset</button>
        </div>
      </fieldset>
    </form>
  </div>
</div>
