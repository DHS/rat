
<?php if ($invites_remaining == 1) { ?>
	
	<p>You have one invite remaining.</p>
	
	<?php $app->loadView('invites_add'); ?>
	
<?php } elseif ($invites_remaining > 1) { ?>

	<p>You have <?php echo $invites_remaining; ?> invites remaining.</p>
	
	<?php $app->loadView('invites_add'); ?>
	
<?php } else { ?>
	
	<p>You have no remaining invites.</p>
	
<?php } ?>

<p>&nbsp;</p>
