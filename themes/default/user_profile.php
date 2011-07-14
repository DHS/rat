
<?php if ($user['full_name'] != NULL || $user['bio'] != NULL || $user['url'] != NULL) { // Only show profile if there is some data ?>

<div class="center_container">
<table class="center">

<?php if ($user['full_name'] != NULL) { ?>
<tr><td class="align_right"><strong>Name</strong></td><td class="align_left" style="padding-left: 10px;"><?php echo $user['full_name']; ?></td></tr>
<?php } if ($user['bio'] != NULL) { ?>
<tr><td class="align_right"><strong>Bio</strong></td><td class="align_left" style="padding-left: 10px;"><?php echo $user['bio']; ?></td></tr>
<?php } if ($user['url'] != NULL) { ?>
<tr><td class="align_right"><strong>URL</strong></td><td class="align_left" style="padding-left: 10px;"><a href="<?php echo $user['url']; ?>" target="_new"><?php echo $user['url']; ?></a></td></tr>
<?php } ?>

</table>
</div>

<p />

<?php } ?>
