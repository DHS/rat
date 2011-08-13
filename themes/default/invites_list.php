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
