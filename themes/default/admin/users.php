
<p>Users: <strong><?php echo count($this->users); ?></strong></p>

<?php $this->loadView('admin/grant_invites'); ?>

<?php //var_dump($this->users); ?>

<?php if (is_array($this->users)) { ?>

<table class="common-table zebra-striped">
  <thead>
    <tr>
      <th>#</th>
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
