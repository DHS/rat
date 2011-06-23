
<div class="center_container">

<h2>Update profile</h2>

<form action="settings.php?page=profile" method="post">

	<table class="center">
		<tr><td class="align_right">Full name:</td><td><input type="text" name="name" value="<?php echo $_SESSION['user']['name']; ?>" /></td></tr>
		<tr><td class="align_right">Bio:</td><td><input type="text" name="bio" value="<?php echo $_SESSION['user']['bio']; ?>" /></td></tr>
		<tr><td class="align_right">URL:</td><td><input type="text" name="url" value="<?php if ($_SESSION['user']['url'] == NULL) { echo 'http://'; } else { echo $_SESSION['user']['url']; } ?>" /></td></tr>
		<tr><td></td><td class="align_left"><input type="submit" value="Save" /></td></tr>
	</table>

</form>

<p>&nbsp;</p>

</div>
