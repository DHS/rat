
<?php if ($invites_remaining == 1) { ?>
	
	<p>You have one invite remaining.</p>
	
	<?php include 'themes/'.$GLOBALS['app']['theme'].'/invites_new.php'; ?>
	
<?php } elseif ($invites_remaining > 1) { ?>

	<p>You have <?php echo $invites_remaining; ?> invites remaining.</p>
	
	<?php include 'themes/'.$GLOBALS['app']['theme'].'/invites_new.php'; ?>
	
<?php } else { ?>
	
	<p>You have no remaining invites.</p>
	
<?php } ?>

<p>&nbsp;</p>
