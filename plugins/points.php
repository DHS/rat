<?php

/*
*	A points system for Rat by @DHS
*
*	Installation
*
*		Comes installed by default
*
*	Usage
*
*		Points are stored in the user object
*
*			$user->points
*
*		To display users's points:
*
*			if (isset($this->plugins->points)) {
*				echo 'You have ' . $user->points.' ' . $this->plugins->points['name'];
*			}
*
*/


class points extends Application {

	function update($user_id, $points) {

		// get current # of points
		$query = mysql_query("SELECT points FROM users WHERE id = $user_id");
		$old_points = mysql_result($query, 0);

		// calculate new # of points
		$new_points = $old_points + $points;

		// update database
		$query = mysql_query("UPDATE users SET points = $new_points WHERE id = $user_id");

	}

	function view() {

		if ($id == $_SESSION['user_id']) {
				echo '<p>You have ' . $user['points'].' ' . $this->plugins->points['name'].'!</p>';
			if ($this->plugins->points['leaderboard'] == TRUE) {
				echo '<p class="small">Where do you rank on the <a href="leaderboard.php">leaderboard</a>?</p>';
			}
		} else {
			echo '<p>' . $user['username'].' has ' . $user['points'].' ' . $this->plugins->points['name'].'!</p>';
			if ($this->plugins->points['leaderboard'] == TRUE) {
				echo '<p class="small">See where they rank on the <a href="leaderboard.php">leaderboard</a>.</p>';
			}
		}

		echo '<p>&nbsp;</p>';

	}

	function view_leaderboard($limit = 10) {

		$query = mysql_query("SELECT id, username, points FROM users WHERE date_joined IS NOT NULL ORDER BY points DESC LIMIT $limit");

		$leaderboard = array();
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$leaderboard[] = $result;
		}

		echo '<table class="common-table zebra-striped">';

		$i = 1;

		foreach ($this->page['leaderboard'] as $row) {

			echo '<tr>
				<td>' . $i.'.</td>
				<td><a href="/' . $row['username'].'">' . $row['username'].'</a></td>
				<td>' . $row['points'].'</td>
			</tr>';

			$i++;

		}

		echo '</table>';

	}

}
