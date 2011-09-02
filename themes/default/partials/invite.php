<form action="<?php echo $this->url_for('invites', 'add'); ?>" method="post">
  <fieldset>
    <legend>Send an invite</legend>
    <label for="email">Email</label>
    <div class="input">
      <input class="medium" name="email" size="50" type="text" value="<?php if (isset($this->uri['params']['email'])) { echo $this->uri['params']['email']; } ?>" />
    </div>
    <div class="actions">
      <button type="submit" class="btn">Invite</button>
    </div>
  </fieldset>
</form>