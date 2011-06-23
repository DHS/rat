
<?php

// Masive if, only show profile if there is some data
if ($user['full_name'] != NULL || $user['bio'] != NULL || $user['url'] != NULL) {

?>

<div class="center_container">
<table class="center">

<?php

if ($user['full_name'] != NULL)
echo '<tr><td class="align_right"><strong>Name</strong></td><td class="align_left" style="padding-left: 10px;">'.$user['full_name'].'</td></tr>';
	
if ($user['bio'] != NULL)
echo '<tr><td class="align_right"><strong>Bio</strong></td><td class="align_left" style="padding-left: 10px;">'.$user['bio'].'</td></tr>';

if ($user['url'] != NULL)
echo '<tr><td class="align_right"><strong>URL</strong></td><td class="align_left" style="padding-left: 10px;"><a href="'.$user['url'].'" target="_new">'.$user['url'].'</a></td></tr>';

?>

</table>
</div>

<p />

<?php

}
// end massive if

?>
