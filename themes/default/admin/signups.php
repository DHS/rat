
<p>Beta signups: <strong><?php echo count($this->page['users']); ?></strong></p>

<?php

if (is_array($this->page['users'])) {

echo '<table>';

foreach ($this->page['users'] as $user) {
	
	if ($user['days_waiting'] == 0) {
		$days_waiting = 'Today!';
	} elseif ($user['days_waiting']) {
		$days_waiting = $user['days_waiting'].' days ago';
	} else {
		$days_waiting = '<span class="bad_news">Error</span>';
	}
	
	if ($user['invites'] > 0) {
		$invite_summary = 'Invited x'.$user['invites'].' &middot;';
	}

	echo '<tr><td>'.$user['email'].'</td><td>
	<form action="'.$this->link_to(NULL, 'admin', 'invite').'" method="post">
	<input type="hidden" name="email" value="'.$user['email'].'">
	'.$days_waiting.' &middot; '.$invite_summary.' <input type="submit" value="Invite" class="btn" />
	</form>
	</td></tr>';

	unset($invite);

}

echo '</table>';

}

?>