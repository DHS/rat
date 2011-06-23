
<div class="center_container">

<p><?php echo $GLOBALS['app']['name']; ?> is an application to demonstrate the basic functionality of <a href="http://github.com/DHS/rat">Rat</a>.</p>

<p>We are currently in beta testing.</p>

<p />

<?php

if ($GLOBALS['app']['beta'] == TRUE)
	include 'themes/'.$GLOBALS['app']['theme'].'/signup_beta.php';

?>

</div>