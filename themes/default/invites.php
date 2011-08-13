
<?php if ($invites_remaining == 1) { ?>
	
	<p>You have one invite remaining.</p>
	
	<?php include 'themes/'.$app->config->theme.'/invites_add.php'; ?>
	
<?php } elseif ($invites_remaining > 1) { ?>

	<p>You have <?php echo $invites_remaining; ?> invites remaining.</p>
	
	<?php include 'themes/'.$app->config->theme.'/invites_add.php'; ?>
	
<?php } else { ?>
	
	<p>You have no remaining invites.</p>
	
<?php } ?>

<p>&nbsp;</p>
