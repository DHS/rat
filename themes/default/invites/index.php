
<div class="row">
  <div class="span8 columns offset4">

<?php if (isset($this->invites_remaining) && $this->invites_remaining >= 1) { ?>

	<?php $this->loadPartial('invite'); ?>

<?php } ?>

<p>&nbsp;</p>

<?php if ( ! empty($this->invites)) { ?>

	<h3>Sent invites</h3>

	<?php foreach ($this->invites as $key => $value) { ?>
		<?php echo htmlentities($value->email); ?>
		<?php if ($value->result >= 1) { ?>
			&middot; <span class="good_news">Accepted</span>
		<?php } else { ?>
			&middot; Still waiting
		<?php } ?>
		<br />
	<?php } ?>

<?php } ?>

  </div>
</div>

<p>&nbsp;</p>
