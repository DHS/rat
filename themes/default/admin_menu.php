<?php

echo '<p>';
	if ($page == 'dashboard') { echo 'Dashboard &middot; '; } else { echo '<a href="admin.php?page=dashboard">Dashboard</a> &middot; '; }
	if ($page == 'config') { echo 'Config &middot; '; } else { echo '<a href="admin.php?page=config">Config</a> &middot; '; }
	if ($page == 'signups') { echo 'Beta signups &middot; '; } else { echo '<a href="admin.php?page=signups">Beta signups</a> &middot; '; }
	if ($page == 'users') { echo 'Users'; } else { echo '<a href="admin.php?page=users">Users</a>'; }
echo '</p>';

?>