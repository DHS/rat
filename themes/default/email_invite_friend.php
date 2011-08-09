<?php

$subject = "[$app->name] An invitation from {$_SESSION['user']['username']}";

$body = '<p>Hi there,</p>
<p>I think you should check out '.$app->name.'! Click the following link to get started:</p>
<p>'.$link.'</p>
<p>Regards,</p>
<p>'.$_SESSION['user']['username'].'</p>';

?>