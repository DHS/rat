
<p>Users: <strong><?php echo count($this->users); ?></strong></p>

<form action="<?php $this->url_for('admin', 'grant_invites') ?>" method="get">
	Grant invites: <input type="text" name="count" value="1" /> <input type="submit" value="Send" class="btn" />
</form>

<?php if (is_array($this->users)) { ?>

<table class="common-table zebra-striped">
  <thead>
    <tr>
      <th>Username</th>
      <th>Credits</th>
      <th>Last login</th>
    </tr>
  </thead>
  <tbody>

<?php foreach ($this->users as $user) { ?>
	
    <tr><td><a href="/<?php echo $user['username']; ?>"><?php echo $user['username']; ?></a></td><td><?php echo $user['points']; ?></td><td><?php echo $user['last_login']; ?></td></tr>

<?php } ?>

  </tbody>
</table>

<?php } ?>
