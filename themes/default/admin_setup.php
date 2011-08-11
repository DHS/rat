
<div class="center_container">

<form action="admin.php?page=invite&id=<?php echo $_GET['id']; ?>" method="POST">

	<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
	
	<table class="center">
		<tr><td class="right">Email:</td><td><input type="text" name="email" value="<?php echo $email; ?>" /></td></tr>
		<tr><td class="right">Username:</td><td><input type="text" name="username" /></td></tr>
		<tr><td class="right">Password:</td><td><input type="password" name="password" value="<?php echo $password; ?>" /></td></tr>
		<tr><td></td><td><input type="submit" value="Send" /></td></tr>
	</table>

</form>

</div>
