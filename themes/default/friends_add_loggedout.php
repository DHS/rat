
<!-- Friend button -->
<div id="friends_<?php echo $user['id']; ?>" class="message">


<?php if ($followers == 1) {
	echo '<strong>One</strong> pioneering follower &middot; ';
} elseif ($followers > 1) {
	echo '<strong>'.$followers.'</strong> followers &middot; ';
} ?>

<a href="login.php?redirect_to=/user.php?id=<?php echo $user['id']; ?>"><input type="button" value="Follow" /></a>

</div>

<p class="clear" />
<!-- End add friend button -->
