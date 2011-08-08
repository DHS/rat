<?php

$body = '<p>Hi there,</p>
<p>Your '.$app->name.' invite is here! Click the following link to get started:</p>
<p>'.$url.'</p>
<p>We value your feedback very highly. Once you\'ve had a play with '.$app->name.', please reply to this email with your thoughts!</p>
<p>Many thanks,</p>
<p>'.$_SESSION['user']['username'].', '.$app->name.' admin</p>';

?>