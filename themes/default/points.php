<?php

if ($id == $_SESSION['user']['id']) {
		echo '<p>You have '.$user['points'].' '.$app->plugins->points['name'].'!</p>';
	if ($app->plugins->points['leaderboard'] == TRUE) {
		echo '<p class="small">Where do you rank on the <a href="leaderboard.php">leaderboard</a>?</p>';
	}
} else {
	echo '<p>'.$user['username'].' has '.$user['points'].' '.$app->plugins->points['name'].'!</p>';
	if ($app->plugins->points['leaderboard'] == TRUE) {
		echo '<p class="small">See where they rank on the <a href="leaderboard.php">leaderboard</a>.</p>';
	}
}

?>

<p>&nbsp;</p>
