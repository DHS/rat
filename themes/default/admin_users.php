
<p>Users: <strong><?php echo $user_count; ?></strong></p>

<?php include 'themes/'.$GLOBALS['app']['theme'].'/admin_grant_invites.php'; ?>

<?php

if (is_array($users)) {

echo '<table>
<tr><td></td><td style="padding-right: 10px;">Credits</td><td style="padding-right: 10px;">Last login</td></tr>';

foreach ($users as $user) {
	
	echo '<tr><td style="padding-right: 10px;"><a href="user.php?id='.$user['id'].'">'.$user['username'].'</a></td><td>'.$user['points'].'</td><td>'.$user['last_login'].'</td></tr>';

}

echo '</table>';

}

?>