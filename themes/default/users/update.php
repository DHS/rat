
<h2>Change password</h2>

<form action="/settings/password" method="post">

	<table>
		<tr><td class="align_right">Old password:</td><td><input type="password" name="old_password" /></td></tr>
		<tr><td class="align_right">New password:</td><td><input type="password" name="new_password1" /></td></tr>
		<tr><td class="align_right">New password again:</td><td><input type="password" name="new_password2" /></td></tr>
		<tr><td></td><td class="align_left"><input type="submit" value="Save" class="btn" /></td></tr>
	</table>

</form>

<p>&nbsp;</p>


<h2>Update profile</h2>

<form action="/settings/profile" method="post">

	<table>
		<tr><td class="align_right">Full name:</td><td><input type="text" name="name" value="<?php echo $_SESSION['user']['name']; ?>" /></td></tr>
		<tr><td class="align_right">Bio:</td><td><input type="text" name="bio" value="<?php echo $_SESSION['user']['bio']; ?>" /></td></tr>
		<tr><td class="align_right">URL:</td><td><input type="text" name="url" value="<?php if ($_SESSION['user']['url'] == NULL) { echo 'http://'; } else { echo $_SESSION['user']['url']; } ?>" /></td></tr>
		<tr><td></td><td class="align_left"><input type="submit" value="Save" class="btn" /></td></tr>
	</table>

</form>

<p>&nbsp;</p>
