
<div class="center_container">

<p><?php echo $app->config->name; ?> is an application to demonstrate the basic functionality of <a href="http://github.com/DHS/rat">Rat</a>.</p>

<p>We are currently in beta testing.</p>

<p />

<?php

if ($app->config->beta == TRUE)
	$app->loadView('signup_beta');

?>

</div>