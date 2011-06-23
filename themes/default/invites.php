<div class="center_container">

<?php

if ($invites_remaining == 1) {
	
	echo '<p>You have one invite remaining.</p>';
	
	include 'themes/'.$GLOBALS['app']['theme'].'/invites_form.php';
	
} elseif ($invites_remaining > 1) {

	echo '<p>You have '.$invites_remaining.' invites remaining.</p>';
	
	include 'themes/'.$GLOBALS['app']['theme'].'/invites_form.php';
	
} else {
	
	echo '<p>You have no remaining invites.</p>';
	
}

?>

</div>

<p>&nbsp;</p>
