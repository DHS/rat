
<form action="<?php echo $this->link_to(NULL, 'invites', 'add'); ?>" method="post">
  <fieldset>
    <legend>Send an invite</legend>
    <label for="email">Email</label>
    <div class="input">
      <input class="medium" name="email" size="50" type="text" value="<?php if (isset($_GET['email'])) { echo $_GET['email']; } ?>" />
    </div>
    <div class="actions">
      <button type="submit" class="btn">Invite</button>
    </div>
  </fieldset>
</form>
