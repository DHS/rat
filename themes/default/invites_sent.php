<?php

if (!empty($invites_sent)) {

	echo '<h2>Sent invites</h2>';
	
	foreach ($invites_sent as $key => $value) {
		echo htmlentities($value['email']);
		if ($value['result'] >= 1) {
			echo ' &middot; <span class="good_news">Accepted</span>';
		} else {
			echo ' &middot; Still waiting';
		}
		echo '<br />';
	}

}

?>

<p>&nbsp;</p>
