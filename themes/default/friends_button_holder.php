
<?php if ($GLOBALS['app']['friends']['enabled'] == TRUE) { ?>

<!-- Friend button -->
<div id="friends_<?php echo $user['id']; ?>" class="message">

<?php

$string = '';

if ($followers == 1) {
	$string = '<strong>One</strong> pioneering follower';
} elseif ($followers > 1) {
	$string = '<strong>'.$followers.'</strong> followers';
}

if ($user['id'] != $_SESSION['user']['id'] && $string != '')
	$string .= ' &middot ';

echo $string;

if ($user['id'] != $_SESSION['user']['id']) {

	if (friends_is($_SESSION['user']['id'], $user['id']) == TRUE) {
	
		// Already friends so show Unfollow button
		include 'themes/'.$GLOBALS['app']['theme'].'/friends_remove.php';
		
	} else {
		
		// Not friends so show Follow button
		include 'themes/'.$GLOBALS['app']['theme'].'/friends_add.php';
		
	}

}

?>

</div>

<p class="clear" />
<!-- End add friend button -->

<?php } ?>
