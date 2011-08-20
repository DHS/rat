
<?php if (isset($app->page->invites_remaining) && $app->page->invites_remaining == 1) { ?>
	
	<p>You have one invite remaining.</p>
	
	<?php $app->loadView('invites/add'); ?>
	
<?php } elseif (isset($app->page->invites_remaining) && $app->page->invites_remaining > 1) { ?>

	<p>You have <?php echo $app->page->invites_remaining; ?> invites remaining.</p>
	
	<?php $app->loadView('invites/add'); ?>
	
<?php } else { ?>
	
	<p>You have no remaining invites.</p>
	
<?php } ?>

<p>&nbsp;</p>

<?php if (!empty($app->page->invites)) { ?>

	<h2>Sent invites</h2>
	
	<?php foreach ($app->page->invites as $key => $value) { ?>
		<?php echo htmlentities($value['email']); ?>
		<?php if ($value['result'] >= 1) { ?>
			&middot; <span class="good_news">Accepted</span>
		<?php } else { ?>
			&middot; Still waiting
		<?php } ?>
		<br />
	<?php } ?>

<?php } ?>

<p>&nbsp;</p>
