
<p>
	<a href="admin.php?page=dashboard">Dashboard</a>
	&middot; <a href="admin.php?page=signups">Beta signups</a>
	&middot; <a href="admin.php?page=users">Users</a>
	<?php
	if (isset($GLOBALS['log']))
		echo ' &middot; <a href="admin.php?page=history">Log</a>';
	?>
</p>
