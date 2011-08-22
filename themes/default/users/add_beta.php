
<div class="center_container">

<form action="/users/add/" method="post">

	<input type="hidden" name="code" value="<?php echo $_GET['code']; ?>" />
	<input type="hidden" name="email" value="<?php echo $_GET['email']; ?>" />
	
		<p>Enter your email address here and we'll let you in soon:</p>
	
	<input type="text" name="email" id="email" value="<?php echo $_GET['email']; ?>" /> <input type="submit" value="Win" />

</form>

</div>
