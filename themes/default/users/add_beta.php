
<div class="row">
  <div class="span8 columns offset4">
    <p>Enter your email address here and we'll let you in soon:</p>
    <form action="<?php echo $this->link_to(NULL, 'users', 'add'); if (isset($_GET['redirect_to'])) { echo '/?redirect_to='.$_GET['redirect_to']; } ?>" method="post">
      <fieldset>
        <?php if (isset($_GET['code'])) { echo '<input type="hidden" name="code" value="'.$_GET['code'].'" />'; } ?>
        <?php if (isset($_GET['email'])) { echo '<input type="hidden" name="email" value="'.$_GET['email'].'" />'; } ?>
        <div class="clearfix">
          <label for="email">Email</label>
          <div class="input">
            <input class="medium" name="email" size="30" type="text" value="<?php if (isset($_GET['email'])) { echo $_GET['email']; } ?>" />
          </div>
        </div> <!-- /clearfix -->
        <div class="actions">
          <button type="submit" class="btn primary">Win</button>
        </div>
      </fieldset>
    </form>
  </div>
</div>
