
<p><?php echo $this->config->name; ?> is an application to demonstrate the basic functionality of <a href="http://github.com/DHS/rat">Rat</a>.</p>

<p>We are currently in beta testing.</p>

<p />

<?php

if ($this->config->beta == TRUE) {
	$this->loadView('signup_beta');
}

?>
