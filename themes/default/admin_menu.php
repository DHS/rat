<?php

echo '<p>';
	if ($page['selector'] == 'dashboard') { echo 'Dashboard &middot; '; } else { echo '<a href="admin.php?page=dashboard">Dashboard</a> &middot; '; }
	if ($page['selector'] == 'signups') { echo 'Beta signups &middot; '; } else { echo '<a href="admin.php?page=signups">Beta signups</a> &middot; '; }
	if ($page['selector'] == 'users') { echo 'Users'; } else { echo '<a href="admin.php?page=users">Users</a>'; }
echo '</p>';

?>