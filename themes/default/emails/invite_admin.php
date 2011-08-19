<?php

$subject = "Your $app->config->name invite is here!";

$body = '<p>Hi there,</p>
<p>Your '.$app->config->name.' invite is here! Click the following link to get started:</p>
<p>'.$link.'</p>
<p>We value your feedback very highly. Once you\'ve had a play with '.$app->config->name.', please reply to this email with your thoughts!</p>
<p>Many thanks,</p>
<p>'.$_SESSION['user']['username'].', '.$app->config->name.' admin</p>';

?>