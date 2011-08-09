<?php

$subject = "[$app->name] Password reset";

$body = '<p>Hi '.$user['username'].',</p>
<p>Here is the link to reset your '.$app->name.' password:</p>
<p>'.$link.'</p>
<p>You should publish another '.$app->items['name'].' to celebrate!</p>
<p>Best regards,</p>
<p>David Haywood Smith, creator of '.$app->name.'</p>';

?>