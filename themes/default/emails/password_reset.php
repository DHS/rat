<?php

$subject = "[$app->config->name] Password reset";

$body = '<p>Hi '.$user['username'].',</p>
<p>Here is the link to reset your '.$app->config->name.' password:</p>
<p>'.$link.'</p>
<p>You should publish another '.$app->config->items['name'].' to celebrate!</p>
<p>Best regards,</p>
<p>David Haywood Smith, creator of '.$app->config->name.'</p>';

?>