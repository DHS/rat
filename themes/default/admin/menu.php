
<p>
	<?php echo $this->link_to('Dashboard', 'admin'); ?>
	&middot; <?php echo $this->link_to('Beta signups', 'admin', 'signups'); ?>
	&middot; <?php echo $this->link_to('Users', 'admin', 'users'); ?>
	<?php if (isset($this->plugins->log)) {
		echo ' &middot; '.$this->link_to('Log', 'admin', 'history');
	} ?>
	
</p>
