
<p>Users: <strong><?php echo $user_count; ?></strong></p>

<?php include 'themes/'.$app->theme.'/admin_grant_invites.php'; ?>

<?php if (is_array($users)) { ?>

<table>
<tr><td></td><td>Credits</td><td>Last login</td></tr>

<?php foreach ($users as $user) { ?>
	
	<tr><td><a href="user.php?id=<?php echo $user['id']; ?>"><?php echo $user['username']; ?></a></td><td><?php echo $user['points']; ?></td><td><?php echo $user['last_login']; ?></td></tr>

<?php } ?>

</table>

<?php } ?>