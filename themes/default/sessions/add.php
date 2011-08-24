
<div class="center_container">

<form action="/sessions/add<?php if($_GET['redirect_to']) echo '?redirect_to='.$_GET['redirect_to']; ?>" method="post">

	<h2>Login</h2>

	<table class="center">
		<tr><td class="align_right">Email:</td><td><input type="text" name="email" value="<?php echo $email; ?>" /></td></tr>
		<tr><td class="align_right">Password:</td><td><input type="password" name="password" /></td></tr>
		<tr><td></td><td class="align_left"><input type="submit" value="Login" /></td></tr>
	</table>

	<p class="small">New to <?php echo $this->config->name ?>? <a href="signup.php<?php if($_GET['redirect_to']) echo '?redirect_to='.$_GET['redirect_to']; ?>">Signup</a> now!
	<br /><a href="reset.php">Forgotten your password</a>?</p>

</form>

</div>
