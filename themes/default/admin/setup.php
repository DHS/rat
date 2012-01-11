<div class="row">
  <div class="span8 columns offset4">
    <form action="<?php echo $this->url_for('admin', 'setup'); ?>" method="post">
	    <fieldset>
        <legend>Enter your details</legend>
        <div class="clearfix">
          <label for="email">Email</label>
          <div class="input">
            <input class="medium" name="email" size="30" type="text" value="" />
          </div>
        </div> <!-- /clearfix -->
        <div class="clearfix">
          <label for="email">Username</label>
          <div class="input">
            <input class="medium" name="username" size="30" type="text" value="" />
          </div>
        </div> <!-- /clearfix -->
        <div class="clearfix">
          <label for="email">Password</label>
          <div class="input">
            <input class="medium" name="password" size="30" type="password" value="" />
          </div>
        </div> <!-- /clearfix -->
        <div class="actions">
          <button type="submit" class="btn primary">Login to your new app!</button>
        </div>
      </fieldset>
    </form>
  </div>
</div>
