<?php

$subject = "[$app->config->name] Welcome to $app->config->name!";

$body = '<p>Hi '.$_POST['username'].',</p>
<p>Thank you for joining '.$app->config->name.'!</p>
<p>We\'ve got some great content - I hope you find something that takes your interest.</p>
<p>Your feedback is very valuable to me so do reply to this email with your thoughts so far.</p>
<p>Thanks again,</p>
<p>David Haywood Smith, creator of '.$app->config->name.'</p>';

?>