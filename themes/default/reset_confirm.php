
<h2>Set new password</h2>

<form action="reset.php" method="post">
	<input type="hidden" name="code" value="<?php echo $_GET['code']; ?>" />
	<table>
	<tr><td class="align_right">New password:</td><td><input type="password" name="password1" id="password1" size="20" /></td></tr>
	<tr><td class="align_right">Confirm new password:</td><td><input type="password" name="password2" id="password2" size="20" /></td></tr>
	<tr><td></td><td><input type="submit" value="Reset" /></td></tr>
	</table>
</form>
