<?php if (!empty($invites_sent)) { ?>

	<h2>Sent invites</h2>
	
	<?php foreach ($invites_sent as $key => $value) { ?>
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
