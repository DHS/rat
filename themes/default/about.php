
<p><?php echo $app['name']; ?> is a web app created by <?php echo $authors; ?> based on the <a href="http://github.com/DHS/rat">rat</a> framework. 

<?php if ($app['beta'] == TRUE) { ?>
It is currently in beta.
<?php } ?>
</p>

<p>It lets you create '<?php echo $app['items']['name_plural']; ?>'<?php 

if ($app['items']['comments'] == TRUE || $app['items']['likes'] == TRUE) {

	echo ' and then ';

	if ($app['items']['comments'] == TRUE)
		echo ' comment on ';
	
	if ($app['items']['comments'] == TRUE && $app['items']['likes'] == TRUE)
		echo ' and ';
	
	if ($app['items']['likes'] == TRUE)
		echo ' \'like\' ';
	
	echo 'them';
	
} ?>. </p>

<?php

if ($app['invites']['enabled'] == TRUE)
	echo '<p>It also has an invite system so that you can invite your friends.</p>';

if (is_object($GLOBALS['points'])) {
	
	echo '<p>It also has a points system';
	
	if ($app['points']['leaderboard'] == TRUE)
		echo ' and a leaderboard so you can see how you\'re doing relative to everyone else';
	
	echo '.</p>';
	
}

if (is_object($GLOBALS['gravatar']))
	echo '<p>'.$app['name'].' is <a href="http://gravatar.com/">Gravatar</a>-enabled.</p>';

?>
