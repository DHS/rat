
<p>Users: <strong><?php echo count($app->page->users); ?></strong></p>

<?php $app->loadView('admin_grant_invites'); ?>

<?php if (is_array($app->page->users)) { ?>

<table>
<tr><td></td><td>Credits</td><td>Last login</td></tr>

<?php foreach ($app->page->users as $user) { ?>
	
	<tr><td><a href="user.php?id=<?php echo $user['id']; ?>"><?php echo $user['username']; ?></a></td><td><?php echo $user['points']; ?></td><td><?php echo $user['last_login']; ?></td></tr>

<?php } ?>

</table>

<?php } ?>